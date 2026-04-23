<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MfsAgent extends Model
{
    protected $fillable = ['name','slug','metadata'];
    protected $casts = ['metadata'=>'array'];

    public function numbers(): HasMany {
        return $this->hasMany(MfsNumber::class);
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }
}