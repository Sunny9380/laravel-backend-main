<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxCharge extends Model
{
    use HasFactory;

    protected $table = "tax_charges";

    protected $fillable = [
        'name',
        'charge',
        'min_order_amount',
        'is_active'
    ];
}
