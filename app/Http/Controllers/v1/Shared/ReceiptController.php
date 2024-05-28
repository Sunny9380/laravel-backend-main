<?php

namespace App\Http\Controllers\v1\Shared;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomTypes;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use View;

class ReceiptController extends Controller
{
    use HttpResponse;

    public function generateReceipt(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|string',
        ]);

        try {
            $booking = Booking::where('booking_id', $validated['booking_id'])->with('hotel')->with('user')->firstOrFail();

            //Getting Vendor id From hotel
            $vendor = $booking->hotel->getVendor;

            $booking->vendor = $vendor;

            $viewName = 'receipt';


            //getting room info
            $bookedRooms = BookedRoom::where('booking_id', $booking->id)->get();

            $rooms = [];
            foreach ($bookedRooms as $bookedRoom) {
                $rooms[] = [
                    'room_id' => $bookedRoom->room_id,
                    'room_count' => $bookedRoom->room_count,
                    'guest_count' => $bookedRoom->guest_count
                ];
            }

            //getting room name
            $roomDetails = [];
            foreach ($rooms as $room) {
                $roomData = Room::where('id', $room['room_id'])->first();
                $room_name = RoomTypes::where('id', $roomData->room_type_id)->first()->name;

                $roomDetails[] = [
                    'room_name' => $room_name,
                    'roomsCount' => $room['room_count'],
                    'guestsCount' => $room['guest_count'],
                ];
            }

            $booking->roomDetails = $roomDetails;

            $booking->formatted_created_at_date = date('d-m-Y', strtotime($booking->created_at));
            $booking->formatted_check_in = date('d-m-Y', strtotime($booking->check_in));
            $booking->formatted_check_out = date('d-m-Y', strtotime($booking->check_out));

            $hotel_availability = $booking->hotel->getVendor->hotel_available_time;

            $booking->formatted_hotel_time_in = Carbon::parse($hotel_availability->time_in)->format('h:i A');
            $booking->formatted_hotel_time_out = Carbon::parse($hotel_availability->time_out)->format('h:i A');

            //checkin Time
            $booking->formatted_check_in_time = Carbon::parse($booking->check_in_time)->format('h:i A');

            $pdf = PDF::setOption('enable-local-file-access', true)->loadView($viewName, ['booking' => $booking])->setOption('enable-internal-links', true);

            $pdf->setOption('enable-local-file-access', true);

            // download pdf file
            return $pdf->download('report.pdf');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Booking not found');
        }
    }
}
