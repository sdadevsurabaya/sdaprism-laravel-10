<?php
namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesUser extends Model
{
    use HasFactory, Cachable;
    protected $guarded = ['id'];

    public function roles()
    {
        return $this->belongsTo(Roles::class, 'roles_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
