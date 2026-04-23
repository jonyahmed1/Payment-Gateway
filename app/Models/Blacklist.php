<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $fillable = ['phone_number','reason','blocked_by','blocked_at','active'];
    protected $casts = ['blocked_at'=>'datetime','active'=>'boolean'];
}