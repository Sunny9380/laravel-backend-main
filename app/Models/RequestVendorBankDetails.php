<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestVendorBankDetails extends Model
{
    use HasFactory;

    protected $table = 'request_vendor_bank_details';

    protected $fillable = [
        'vendor_id',
        'account_name',
        'account_email',
        'ifsc_code',
        'account_number',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
