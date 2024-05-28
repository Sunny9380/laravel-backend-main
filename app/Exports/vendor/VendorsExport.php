<?php

namespace App\Exports\vendor;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorsExport implements FromArray, WithHeadings
{

    protected $vendor_id, $isAllVendors;

    public function __construct($vendor_id=null, $isAllVendors=1)
    {
        $this->vendor_id = $vendor_id;
        $this->isAllVendors = $isAllVendors;

    }

    public function array(): array
    {

        $vendors = null;

        if ($this->isAllVendors) {
            $vendors = Vendor::all();
        } else {
            $vendors = Vendor::where('vendor_id', $this->vendor_id)->get();
        }

        $exportData = $vendors->map(function ($vendor) {
            return [
                'Id' => $vendor->id,
                'Vendor ID' => $vendor->vendor_id,
                'Email' => $vendor->email,
                'Name' => $vendor->name,
                'Address' => $vendor->address,
                'Phone Number' => $vendor->phone_number,
                'Properties' => count($vendor->properties),
                'GST Number' => $vendor->gst_number,
                'Status' => $vendor->status,
                'Created At' => $vendor->created_at,
            ];
        });

        return [$exportData];
    }

    public function headings(): array
    {
        // Define the column headings
        return [
            'Id',
            'Vendor ID',
            'Email',
            'Name',
            'Address',
            'Phone Number',
            'Properties',
            'GST Number',
            'Status',
            'Created At'
        ];
    }
}
