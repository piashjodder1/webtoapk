<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

class PlansPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $title = 'Pricing & Plans';

    protected static ?string $navigationLabel = 'Plans';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $slug = 'plans';

    protected string $view = 'filament.user.pages.plans-page';

    public function mount(): void
    {
        if (session()->has('error')) {
            \Filament\Notifications\Notification::make()
                ->title('No Credits Remaining')
                ->body(session('error'))
                ->danger()
                ->send();
        }
    }

    public function getViewData(): array
    {
        return [
            'plans' => \App\Models\Plan::where('is_active', true)->get(),
            'gateways' => \App\Models\PaymentGateway::where('is_active', true)->get(),
            'activePlanId' => auth()->user()->activeSubscription?->plan_id,
            'pendingPlanIds' => \App\Models\Payment::where('user_id', auth()->id())->where('status', 'pending')->pluck('plan_id')->toArray(),
        ];
    }

    public function purchaseAction(): \Filament\Actions\Action
    {
        // Pre-build dynamic fields for all active manual gateways
        $dynamicFields = [];
        $gateways = \App\Models\PaymentGateway::where('is_active', true)->where('type', 'manual')->get();
        
        foreach ($gateways as $gateway) {
            $fields = $gateway->user_input_fields ?? [];
            foreach ($fields as $index => $field) {
                $fieldName = 'dynamic_' . $gateway->id . '_' . \Illuminate\Support\Str::slug($field['name']);
                
                if (($field['type'] ?? 'text') === 'file') {
                    $component = \Filament\Forms\Components\FileUpload::make($fieldName)
                        ->label($field['name'])
                        ->image()
                        ->directory('payment_proofs');
                } else {
                    $component = \Filament\Forms\Components\TextInput::make($fieldName)
                        ->label($field['name']);
                }

                $component->required(fn ($get) => $get('payment_gateway_id') == $gateway->id && !empty($field['required']))
                          ->hidden(fn ($get) => $get('payment_gateway_id') != $gateway->id);
                          
                $dynamicFields[] = $component;
            }
        }

        return \Filament\Actions\Action::make('purchaseAction')
            ->label('Purchase Plan')
            ->color('primary')
            ->modalSubmitActionLabel('Payment')
            ->extraAttributes(['class' => 'w-full py-3.5 rounded-2xl font-black text-sm tracking-wide transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5'])
            ->form(array_merge([
                \Filament\Forms\Components\Select::make('payment_gateway_id')
                    ->label('Select Payment Method')
                    ->options(\App\Models\PaymentGateway::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->live()
                    // Clear all dynamic fields when gateway changes
                    ->afterStateUpdated(function ($set, $state) use ($gateways) {
                        foreach ($gateways as $gateway) {
                            $fields = $gateway->user_input_fields ?? [];
                            foreach ($fields as $field) {
                                $set('dynamic_' . $gateway->id . '_' . \Illuminate\Support\Str::slug($field['name']), null);
                            }
                        }
                    }),

                \Filament\Forms\Components\Placeholder::make('instructions')
                    ->label('Payment Instructions')
                    ->hidden(fn ($get) => ! $get('payment_gateway_id'))
                    ->content(function ($get) {
                        $gateway = \App\Models\PaymentGateway::find($get('payment_gateway_id'));
                        if (! $gateway) return '';
                        
                        if ($gateway->type === 'automated') {
                            return 'You will be redirected to the secure payment gateway to complete your purchase.';
                        }

                        $html = '<div class="bg-gray-50 p-4 rounded-lg text-sm space-y-3 border border-gray-100">';
                        
                        if ($gateway->payment_instructions) {
                            $html .= "<div class='text-gray-800 mb-2'>" . nl2br(e($gateway->payment_instructions)) . "</div>";
                        }

                        $creds = $gateway->credentials ?? [];
                        if (count($creds) > 0) {
                            $html .= "<div class='mt-3 bg-white rounded-xl border border-gray-200 overflow-hidden'>";
                            foreach ($creds as $key => $value) {
                                $label = strtoupper(str_replace('_', ' ', $key));
                                // Escaping single quotes for JS
                                $safeValue = addslashes($value);
                                $html .= "
                                <div class='flex flex-wrap sm:flex-nowrap items-center justify-between p-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors gap-2'>
                                    <div class='flex flex-wrap items-baseline gap-2 overflow-hidden flex-1'>
                                        <strong class='text-gray-900 text-sm whitespace-nowrap'>{$label}:</strong> 
                                        <span class='text-gray-900 font-mono text-sm break-all'>{$value}</span>
                                    </div>
                                    <button type='button' 
                                        onclick=\"navigator.clipboard.writeText('{$safeValue}'); new FilamentNotification().title('Copied!').success().send();\"
                                        class='text-primary-600 hover:text-primary-800 transition-colors p-1.5 rounded-lg hover:bg-primary-50 flex-shrink-0'
                                        title='Copy to clipboard'
                                    >
                                        <svg xmlns='http://www.w3.org/2000/svg' class='w-5 h-5' fill='none' viewBox='0 0 24 24' stroke='currentColor' stroke-width='2'>
                                            <path stroke-linecap='round' stroke-linejoin='round' d='M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z' />
                                        </svg>
                                    </button>
                                </div>";
                            }
                            $html .= "</div>";
                        }
                        
                        $html .= '</div>';
                        return new \Illuminate\Support\HtmlString($html);
                    }),

            ], $dynamicFields))
            ->action(function (array $data, array $arguments) {
                $plan = \App\Models\Plan::findOrFail($arguments['plan_id']);
                
                // Prevent duplicate pending requests
                if (\App\Models\Payment::where('user_id', auth()->id())->where('plan_id', $plan->id)->where('status', 'pending')->exists()) {
                    \Filament\Notifications\Notification::make()
                        ->title('Pending Request Exists')
                        ->body('You already have a pending payment request for this plan.')
                        ->warning()
                        ->send();
                    return;
                }

                $gateway = \App\Models\PaymentGateway::findOrFail($data['payment_gateway_id']);

                if ($gateway->type === 'automated') {
                    return redirect()->route('payment.checkout', [
                        'plan' => $plan->id, 
                        'gateway' => $gateway->id
                    ]);
                }

                // Extract dynamic user provided data
                $userProvidedData = [];
                $prefix = 'dynamic_' . $gateway->id . '_';
                foreach ($data as $key => $value) {
                    if (str_starts_with($key, $prefix)) {
                        $actualName = str_replace($prefix, '', $key);
                        $userProvidedData[$actualName] = $value;
                    }
                }

                // Manual Payment Logic
                \App\Models\Payment::create([
                    'user_id' => auth()->id(),
                    'plan_id' => $plan->id,
                    'payment_gateway_id' => $gateway->id,
                    'amount' => $plan->price,
                    'status' => 'pending',
                    'invoice_id' => strtoupper(uniqid('INV-')),
                    'user_provided_data' => $userProvidedData,
                ]);

                \Filament\Notifications\Notification::make()
                    ->title('Payment Submitted Successfully')
                    ->body('Your payment is pending manual verification by an admin. Your quota will be updated once approved.')
                    ->success()
                    ->send();
            });
    }
}
