<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';

    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_APPROVED = 'approved';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id','mfs_agent_id','mfs_number_id','type',
        'amount','currency','trx_id','status','metadata',
        'maker_admin_id','checker_admin_id','approved_at','verified_at','rejected_at','notes'
    ];

    protected $casts = [
        'metadata'=>'array',
        'amount'=>'decimal:2',
        'approved_at'=>'datetime',
        'verified_at'=>'datetime',
        'rejected_at'=>'datetime'
    ];

    public function number(): BelongsTo { return $this->belongsTo(MfsNumber::class,'mfs_number_id'); }
    public function agent(): BelongsTo { return $this->belongsTo(MfsAgent::class,'mfs_agent_id'); }
    public function maker() : BelongsTo { return $this->belongsTo(\App\Models\User::class,'maker_admin_id'); }
    public function checker() : BelongsTo { return $this->belongsTo(\App\Models\User::class,'checker_admin_id'); }
    public function attachments() { return $this->hasMany(TransactionAttachment::class); }
}