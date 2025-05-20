<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'user_id',
        'device_type',
        'device_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}