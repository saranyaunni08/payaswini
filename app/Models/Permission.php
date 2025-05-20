<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $primaryKey = 'permission_id';
    protected $fillable = ['role_id', 'can_add_customer', 'can_add_agent', 'can_edit_delete', 'edit_delete_time_limit'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}