<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'address',
        'profile_status',
        'approved_by',
        'approved_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'customer_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'customer_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(ProfileStatusLog::class, 'customer_id');
    }
}