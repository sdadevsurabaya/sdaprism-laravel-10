<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory, Cachable;
    protected $guarded = ['id'];

    public function pricelists()
    {
        return $this->hasMany(Pricelist::class);
    }
}
