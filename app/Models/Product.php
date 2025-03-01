<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Product extends Model
{
    use HasFactory,Cachable;
    protected $table = 'products';
    protected $fillable = [
        'sda_global_number',
        'product_name',
        'unit',
        'created_at',
        'updated_at',

    ];



}
