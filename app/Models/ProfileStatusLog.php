<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileStatusLog extends Model
{
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'customer_id',
        'changed_by',
        'old_status',
        'new_status',
        'created_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}