<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Subscription;

class PaymentObserver
{
    /**
     * Handle the Payment "saved" event.
     */
    public function saved(Payment $payment): void
    {
        if ($payment->wasChanged('status') && $payment->status === 'completed') {
            $this->activateSubscription($payment);
        }
    }

    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        if ($payment->status === 'completed') {
            $this->activateSubscription($payment);
        }
    }

    /**
     * Activate subscription for the payment.
     */
    protected function activateSubscription(Payment $payment): void
    {
        $user = $payment->user;
        $oldSub = $user ? $user->activeSubscription : null;
        $oldCredits = $oldSub ? $oldSub->remaining_credits : 0;

        // Deactivate old active subscriptions for the user
        if ($oldSub) {
            $oldSub->update(['status' => 'expired']);
        } else {
            // Fallback in case of multiple active ones
            Subscription::where('user_id', $payment->user_id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);
        }

        // Calculate cumulative credits
        $newPlanCredits = $payment->plan ? $payment->plan->max_apps : 0;
        $totalCredits = $newPlanCredits + $oldCredits;

        // Create a new active subscription with cumulative credits
        Subscription::create([
            'user_id' => $payment->user_id,
            'plan_id' => $payment->plan_id,
            'status' => 'active',
            'remaining_credits' => $totalCredits,
            'used_credits' => 0,
        ]);
    }
}
