<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'roles_users');
    }

    public function rolesUsers(){
        return $this->hasMany(RolesUser::class, 'roles_id');
    }
}
