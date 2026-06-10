<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class PaymentController extends Controller
{
    public function checkout(Plan $plan, PaymentGateway $gateway)
    {
        if ($gateway->type !== 'automated') {
            abort(400, 'Invalid gateway type.');
        }

        // Get Credentials from DB
        $creds = $gateway->credentials ?? [];
        $apiKey = $creds['api_key'] ?? '';
        $apiUrl = $creds['api_url'] ?? '';

        if (empty($apiKey) || empty($apiUrl)) {
            return redirect()->route('filament.user.pages.plans')->with('error', 'Payment gateway is not properly configured.');
        }

        $user = auth()->user();

        $response = Http::withHeaders([
            'RT-UDDOKTAPAY-API-KEY' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(rtrim($apiUrl, '/') . '/api/checkout-v2', [
            'full_name' => $user->name,
            'email' => $user->email,
            'amount' => $plan->price,
            'metadata' => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'gateway_id' => $gateway->id,
            ],
            'redirect_url' => route('payment.success', ['gateway' => $gateway->id]),
            'cancel_url' => route('payment.cancel', ['gateway' => $gateway->id]),
            'webhook_url' => route('payment.webhook', ['gateway' => $gateway->id]),
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['status']) && $data['status'] && isset($data['payment_url'])) {
                return redirect()->away($data['payment_url']);
            }
        }

        // Add a notification instead of session error to match standard UX
        Notification::make()
            ->title('Failed to initiate payment.')
            ->body($response->json('message') ?? 'Please check the gateway configuration.')
            ->danger()
            ->send();
            
        return redirect()->route('filament.user.pages.plans');
    }

    public function success(Request $request, PaymentGateway $gateway)
    {
        $invoiceId = $request->input('invoice_id');
        
        if ($invoiceId) {
            $creds = $gateway->credentials ?? [];
            $apiKey = $creds['api_key'] ?? '';
            $apiUrl = $creds['api_url'] ?? '';

            if ($apiKey && $apiUrl) {
                // Verify the payment
                $verifyResponse = Http::withHeaders([
                    'RT-UDDOKTAPAY-API-KEY' => trim($apiKey),
                    'Content-Type' => 'application/json',
                ])->post(rtrim($apiUrl, '/') . '/api/verify-payment', [
                    'invoice_id' => $invoiceId
                ]);

                if ($verifyResponse->successful()) {
                    $data = $verifyResponse->json();
                    if (isset($data['status']) && $data['status'] === 'COMPLETED') {
                        $metadata = $data['metadata'] ?? [];
                        
                        // Parse JSON string metadata if necessary
                        if (is_string($metadata)) {
                            $metadata = json_decode($metadata, true);
                        }

                        if (isset($metadata['user_id'], $metadata['plan_id'])) {
                            $existingPayment = Payment::where('invoice_id', $invoiceId)->first();
                            
                            if (! $existingPayment) {
                                $filteredData = array_filter([
                                    'Full Name' => $data['full_name'] ?? null,
                                    'Invoice ID' => $invoiceId,
                                    'Sender Number' => $data['sender_number'] ?? null,
                                    'Transaction ID' => $data['transaction_id'] ?? null,
                                    'Payment Method' => $data['payment_method'] ?? null,
                                ]);

                                Payment::create([
                                    'user_id' => $metadata['user_id'],
                                    'plan_id' => $metadata['plan_id'],
                                    'payment_gateway_id' => $gateway->id,
                                    'amount' => $data['amount'] ?? 0,
                                    'status' => 'completed',
                                    'invoice_id' => $invoiceId,
                                    'user_provided_data' => $filteredData,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        Notification::make()
            ->title('Payment Successful')
            ->body('Your payment was successful and your limits have been updated.')
            ->success()
            ->send();

        return redirect('/dashboard');
    }

    public function cancel(Request $request, PaymentGateway $gateway)
    {
        Notification::make()
            ->title('Payment Cancelled')
            ->body('You have cancelled the payment process.')
            ->warning()
            ->send();

        return redirect('/dashboard');
    }

    public function webhook(Request $request, PaymentGateway $gateway)
    {
        \Log::info('UddoktaPay Webhook Received', $request->all());
        
        $creds = $gateway->credentials ?? [];
        $apiKey = $creds['api_key'] ?? '';

        // Verify the webhook using the API Key
        $headerApiKey = $request->header('RT-UDDOKTAPAY-API-KEY');

        if (trim($headerApiKey) !== trim($apiKey)) {
            \Log::warning('UddoktaPay Webhook Unauthorized', ['header' => $headerApiKey, 'expected' => $apiKey]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $status = $request->input('status');
        $metadata = $request->input('metadata');
        
        // Sometimes metadata comes as a JSON string
        if (is_string($metadata)) {
            $metadata = json_decode($metadata, true);
        }

        \Log::info('UddoktaPay Webhook Data', ['status' => $status, 'metadata' => $metadata]);

        if ($status === 'COMPLETED' && isset($metadata['user_id'], $metadata['plan_id'])) {
            $userId = $metadata['user_id'];
            $planId = $metadata['plan_id'];
            $invoiceId = $request->input('invoice_id');

            // Find if this payment was already processed
            $existingPayment = Payment::where('invoice_id', $invoiceId)->first();

            if (! $existingPayment) {
                // Record the completed payment
                $data = $request->all();
                
                $filteredData = array_filter([
                    'Full Name' => $data['full_name'] ?? null,
                    'Invoice ID' => $invoiceId,
                    'Sender Number' => $data['sender_number'] ?? null,
                    'Transaction ID' => $data['transaction_id'] ?? null,
                    'Payment Method' => $data['payment_method'] ?? null,
                ]);

                $payment = Payment::create([
                    'user_id' => $userId,
                    'plan_id' => $planId,
                    'payment_gateway_id' => $gateway->id,
                    'amount' => $request->input('amount') ?? 0,
                    'status' => 'completed',
                    'invoice_id' => $invoiceId,
                    'user_provided_data' => $filteredData,
                ]);
                \Log::info('UddoktaPay Webhook Payment Created', ['payment_id' => $payment->id]);
            } else {
                \Log::info('UddoktaPay Webhook Payment Already Exists', ['invoice_id' => $request->input('invoice_id')]);
            }
        } else {
            \Log::warning('UddoktaPay Webhook Condition Failed', [
                'status_match' => $status === 'COMPLETED',
                'isset_metadata' => isset($metadata['user_id'], $metadata['plan_id'])
            ]);
        }

        return response()->json(['message' => 'Webhook received successfully']);
    }
}
