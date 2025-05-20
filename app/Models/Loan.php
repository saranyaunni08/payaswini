<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $primaryKey = 'loan_id';
    protected $fillable = ['customer_id', 'agent_id', 'amount', 'interest_rate', 'start_date', 'duration_months', 'status'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function collectionAgent()
    {
        return $this->belongsTo(CollectionAgent::class, 'agent_id', 'agent_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'loan_id', 'loan_id');
    }
}
