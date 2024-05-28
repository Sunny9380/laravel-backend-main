<?php

namespace App\Http\Controllers\v1\admin;

use App\Exports\Reports\Booking;
use App\Exports\Reports\BookingCancellation;
use App\Exports\Reports\Complain;
use App\Exports\Reports\Coupon;
use App\Exports\Reports\PropertyOccupancy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\HttpResponse;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use HttpResponse;

    public function getComplainReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'from' => 'required|date',
                'to' => 'required|date',
                'vendor_id' => 'nullable|exists:vendor,vendor_id',
                'is_all_vendors' => 'nullable|boolean'
            ]);

            $from = $validated['from'];
            $to = $validated['to'];
            $vendorId = $request->vendor_id ? $validated['vendor_id'] : null;
            $isAllVendors = $validated['is_all_vendors'];

            if (!$isAllVendors && !$vendorId)
                return $this->internalError(
                    message: 'Vendor id is required'
                );

            return Excel::download(new Complain($from, $to, $vendorId, $isAllVendors), 'complain_report.xlsx');

        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }

    public function getCouponReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'from' => 'required|date',
                'to' => 'required|date',
            ]);

            $from = $validated['from'];
            $to = $validated['to'];

            return Excel::download(new Coupon($from, $to), 'coupon_report.xlsx');

        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }

    public function getPropertyOccupancyReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'from' => 'required|date',
                'to' => 'required|date',
                'vendor_id' => 'nullable|exists:vendor,vendor_id',
                'is_all_vendors' => 'nullable|boolean'
            ]);

            $from = $validated['from'];
            $to = $validated['to'];
            $vendorId = $request->vendor_id ? $validated['vendor_id'] : null;
            $isAllVendors = $validated['is_all_vendors'];

            if (!$isAllVendors && !$vendorId)
                return $this->internalError(
                    message: 'Vendor id is required'
                );

            return Excel::download(new PropertyOccupancy($from, $to, $vendorId, $isAllVendors), 'occupancy_report.xlsx');

        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }

    public function getBookingCancellationReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'from' => 'required|date',
                'to' => 'required|date',
                'vendor_id' => 'nullable|exists:vendor,vendor_id',
                'is_all_vendors' => 'nullable|boolean'
            ]);

            $from = $validated['from'];
            $to = $validated['to'];
            $vendorId = $request->vendor_id ? $validated['vendor_id'] : null;
            $isAllVendors = $validated['is_all_vendors'];

            if (!$isAllVendors && !$vendorId)
                return $this->internalError(
                    message: 'Vendor id is required'
                );


            return Excel::download(new BookingCancellation($from, $to, $vendorId, $isAllVendors), 'booking_cancellation_report.xlsx');

        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }

    public function getBookingReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'from' => 'required|date',
                'to' => 'required|date',
            ]);

            $from = $validated['from'];
            $to = $validated['to'];

            return Excel::download(new Booking($from, $to), 'booking_report.xlsx');

        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }
}
