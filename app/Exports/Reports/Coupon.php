<?php

namespace App\Exports\Reports;

use App\Models\BookingType;
use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Coupon implements FromArray, WithHeadings
{

    protected $from, $to;

    public function __construct($from, $to)
    {
        $this->from = $from . ' 00:00:00';
        $this->to = $to . ' 23:59:59';
    }

    public function array(): array
    {

        $bookings = \App\Models\Booking::with('hotel', 'user', 'coupon')
            ->whereNotNull('coupon_id')
            ->where('is_cancelled', 0)
            ->where('created_at', '>=', $this->from)
            ->where('created_at', '<=', $this->to)
            ->get();

        $exportData = $bookings->map(function ($booking) {
            return [
                'Booking Id' => $booking->booking_id,
                'Order Id' => $booking->order_id,
                'Payment Status' => $booking->payment_status,
                'Amount' => $booking->amount,
                'Check In' => $booking->check_in,
                'Check Out' => $booking->check_out,
                'Check In Hours' => $booking->check_in_hours,
                'Check In Time' => $booking->check_in_time,
                'Coupon' => $booking->coupon->name ?? '',
                'User Name' => $booking->name,
                'Email' => $booking->email,
                'Registered Name' => $booking->user->name,
                'Registered Email' => $booking->user->email ?? '',
                'Phone Number' => $booking->primary_phone_number,
                'Secondary Phone Number' => $booking->secondary_phone_number,
                'Vendor Id' => Vendor::where('id', $booking->hotel->vendor_id)->first()->vendor_id,
                'Property Name' => $booking->hotel->name,
                'Booking Type' => BookingType::where('id', $booking->booking_type)->first()->name,
            ];
        });

        return [$exportData];
    }

    public function headings(): array
    {
        // Define the column headings
        return [
            'Booking Id',
            'Order Id',
            'Payment Status',
            'Amount',
            'Check In',
            'Check Out',
            'Check In Hours',
            'Check In Time',
            'Coupon',
            'User Name',
            'Email',
            'Registered Name',
            'Registered Email',
            'Phone Number',
            'Secondary Phone Number',
            'Vendor Id',
            'Property Name',
            'Booking Type',
        ];
    }

}
