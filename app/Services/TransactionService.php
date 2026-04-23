<?php
namespace App\Services;
use App\Models\Transaction;
use App\Models\Blacklist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Exception;

class TransactionService
{
    // Attempts to create a transaction ensuring trx deduplication.
    // Throws ValidationException on duplicate.
    public function create(array $data, $submitterUserId = null): Transaction
    {
        $mfsAgentId = $data['mfs_agent_id'];
        $trxId = $data['trx_id'];

        // Check blacklist first
        if (!empty($data['phone_number'])) {
            $black = Blacklist::where('phone_number', $data['phone_number'])->where('active', true)->first();
            if ($black) {
                throw ValidationException::withMessages(['phone_number' => 'This phone number is blacklisted.']);
            }
        }

        $lockKey = "trx:{$mfsAgentId}:{$trxId}";
        // Use Redis lock via Cache::lock (requires Redis)
        $lock = Cache::lock($lockKey, 10);
        if (!$lock->get()) {
            // Another worker is processing same trx
            throw ValidationException::withMessages(['trx_id' => 'Transaction is currently being processed. Try again shortly.']);
        }

        try {
            return DB::transaction(function () use ($data, $submitterUserId) {
                // Final DB-level uniqueness prevents duplicates in race condition
                $exists = Transaction::where('trx_id', $data['trx_id'])
                                     ->where('mfs_agent_id', $data['mfs_agent_id'])
                                     ->first();

                if ($exists) {
                    throw ValidationException::withMessages(['trx_id' => 'Duplicate trx_id for this agent. See existing transaction id '.$exists->id]);
                }

                $tx = Transaction::create(array_merge($data, [
                    'user_id' => $submitterUserId,
                    'status' => Transaction::STATUS_PENDING,
                ]));

                return $tx;
            });
        } finally {
            $lock->release();
        }
    }
}