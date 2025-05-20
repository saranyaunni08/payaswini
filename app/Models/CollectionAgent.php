<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CollectionAgent extends Model
{
    protected $primaryKey = 'agent_id';
    protected $fillable = [
        'user_id',
        'agent_code',
        'full_name',
        'phone',
        'address',
        'profile_status',
        'date_of_joining',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'agent_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'agent_id');
    }
}