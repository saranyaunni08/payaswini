<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $primaryKey = 'document_id';
    protected $fillable = ['customer_id','agent_id', 'document_type', 'file_path'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
    public function agent()
    {
        return $this->belongsTo(CollectionAgent::class, 'agent_id');
    }
}