<?php

namespace App\Exports;

use App\Models\RequestVendorBankDetails;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBankDetails;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportVendorBankDetails implements FromArray, WithHeadings
{
    private $id;
    private $is_request;

    public function __construct($request_data)
    {
        $this->id = $request_data['id'];
        $this->is_request = $request_data['is_request'];
    }

    public function array(): array
    {
        $requestDetails = null;

        if($this->is_request) {
            // Get the vendor bank details from the request
            $requestDetails = RequestVendorBankDetails::where('id', $this->id)->first();
        } else {
            // Get the vendor bank details from the vendor bank details table
            $requestDetails = VendorBankDetails::where('id', $this->id)->first();
        }

        $vendor = Vendor::where('id', $requestDetails->vendor_id)->first();

        // Merge user and vendor bank details into a single array
        $exportData = [
            $requestDetails->account_name,
            $requestDetails->account_email,
            1,
            1,
            $vendor->name,
            'testing...',
            $vendor->phone_number,
            $requestDetails->ifsc_code,
            $requestDetails->account_number,
            'admin...'
        ];

        return [$exportData];
    }

    public function headings(): array
    {
        // Define the column headings
        return [
            'account_name',
            'account_email',
            'dashboard_access',
            'customer_refunds',
            'business_name',
            'business_type',
            'phone_number',
            'ifsc_code',
            'account_number',
            'beneficiary_name',
        ];
    }
}
