<?php
namespace App\Jobs;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyIntegrators implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Transaction $transaction;

    public function __construct(Transaction $transaction) { $this->transaction = $transaction; }

    public function handle()
    {
        // Example: fetch integrator webhooks from DB and POST. Implement HMAC signing.
        $webhooks = \DB::table('webhook_subscriptions')->where('agent_id', $this->transaction->mfs_agent_id)->get();
        foreach ($webhooks as $w) {
            try {
                Http::withHeaders(['X-Signature' => hash_hmac('sha256', json_encode($this->transaction->toArray()), $w->secret)])
                    ->post($w->url, ['transaction' => $this->transaction]);
            } catch (\Exception $e) {
                // log and retries handled by queue failed jobs
                \Log::error('NotifyIntegrators error: '.$e->getMessage());
            }
        }
    }
}