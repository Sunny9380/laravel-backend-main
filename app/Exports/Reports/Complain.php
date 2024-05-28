<?php

namespace App\Exports\Reports;

use App\Models\BookingType;
use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Complain implements FromArray, WithHeadings
{

    protected $from, $to, $vendorId, $isAllVendors;

    public function __construct($from, $to, $vendorId, $isAllVendors)
    {
        $this->from = $from . ' 00:00:00';
        $this->to = $to . ' 23:59:59';
        $this->vendorId = $vendorId;
        $this->isAllVendors = $isAllVendors;
    }

    public function array(): array
    {
        $complains = null;
        if ($this->isAllVendors) {
            $complains = \App\Models\Complain::
                where('created_at', '>=', $this->from)
                ->where('created_at', '<=', $this->to)
                ->with('user', 'property')
                ->get();
            foreach ($complains as $complain) {
                $complain->vendor_id = $complain->property->vendor->vendor_id;
            }
            // V-67fx0w
        } else {
            $vendor = Vendor::where('vendor_id', $this->vendorId)
                ->with('properties')
                ->first();

            foreach ($vendor->properties as $property) {
                $complains = \App\Models\Complain::
                    where('property_id', $property->id)
                    ->where('created_at', '>=', $this->from)
                    ->where('created_at', '<=', $this->to)
                    ->with('user', 'property')
                    ->get();
            }
            foreach ($complains as $complain) {
                $complain->vendor_id = $this->vendorId;
            }
        }

        $exportData = $complains->map(function ($complain) {
            return [
                'User Name' => $complain->user->name,
                'Property Name' => $complain->property->name,
                'Vendor ID' => $complain->vendor_id,
                'Complain Title' => $complain->title,
                'Complain Description' => $complain->description,
                'Complain Status' => $complain->status,
                'Created At' => $complain->created_at
            ];
        });
        return [$exportData];
    }

    public function headings(): array
    {
        // Define the column headings
        return [
            'User Name',
            'Property Name',
            'Vendor ID',
            'Complain Title',
            'Complain Description',
            'Complain Status',
            'Created At'
        ];
    }

}
