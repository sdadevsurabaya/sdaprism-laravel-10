<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory, Cachable;
    protected $guarded = ['id'];

    public function headerLogo()
    {
        return $this->belongsTo(HeaderLogo::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
