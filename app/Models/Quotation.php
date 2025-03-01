<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quotation';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no',
        'address_letter',
        'customer_id',
        'phone',
        'payment_type',
        'currency',
        'date',
        'valid_until',
        'address',
        'contact_person',
        'descriptions',
        'remarks',
        'account_options',
        'made_by',
        'sub_total',
        'additional_discount',
        'additional_cost',
        'total',
        'deposit',
        'grand_total',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'valid_until' => 'date',
        'sub_total' => 'float',
        'additional_discount' => 'float',
        'additional_cost' => 'float',
        'total' => 'float',
        'deposit' => 'float',
        'grand_total' => 'float',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date',
        'valid_until',
    ];

    public function QuotationProduct()
    {
        return $this->hasMany(DetailQuotationProduct::class);
    }
}
