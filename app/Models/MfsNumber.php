<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MfsNumber extends Model
{
    protected $fillable = ['mfs_agent_id','phone_number','label','metadata','is_active'];
    protected $casts = ['metadata'=>'array','is_active'=>'boolean'];

    public function agent(): BelongsTo {
        return $this->belongsTo(MfsAgent::class, 'mfs_agent_id');
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class, 'mfs_number_id');
    }
}