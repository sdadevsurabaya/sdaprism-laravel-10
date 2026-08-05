<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Cachable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'roles_users','users_id','roles_id');
    }

    public function hasRole($role)
    {
        return $this->roles()->whereRaw('LOWER(name) = ?', [strtolower($role)])
            ->exists();
    }

    public function rolesUsers()
    {
        return $this->hasMany(RolesUser::class, 'users_id');
    }

    public function priceLists(){
        return $this->hasMany(PriceList::class, 'user_id');
    }

    public function pricelistWhitelist()
    {
        return $this->hasOne(PricelistApiWhitelist::class, 'user_id');
    }

    public function canAccessRealtimePricelist(): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return PricelistApiWhitelist::where('user_id', $this->id)->exists();
    }
}

