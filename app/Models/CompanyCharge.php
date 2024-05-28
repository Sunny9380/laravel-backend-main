<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCharge extends Model
{
    use HasFactory;

    protected $table = "company_charges";

    protected $fillable = [
        'name',
        'is_percent',
        'value',
        'is_active',
    ];
}
