<?php

namespace App\Console\Commands;

use App\Models\PaymentTransaction;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupPendingTransactions extends Command
{
    protected $signature = 'transactions:cleanup-pending';
    protected $description = 'Cancel transactions pending for more than 30 minutes';

    public function handle()
    {
        $threshold = Carbon::now()->subMinutes(30);

        $pendingTransactions = PaymentTransaction::where('status', 'pending')
            ->where('created_at', '<', $threshold)
            ->get();

        foreach ($pendingTransactions as $transaction) {
            // Update Transaction
            $transaction->update([
                'status' => 'cancelled',
                'failure_reason' => 'Expired without completion (No webhook)'
            ]);

            // Update Subscription if exists
            if ($transaction->subscription) {
                $transaction->subscription->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Payment expired'
                ]);

                // Reset User Status if this was their only pending sub
                $user = $transaction->subscription->user;
                if (!$user->hasActiveSubscription()) {
                    $user->update([
                        'subscription_status' => 'inactive'
                    ]);
                }
            }
        }

        $this->info("Cleaned up {$pendingTransactions->count()} pending transactions.");
        return Command::SUCCESS;
    }
}
