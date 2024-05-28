<?php

namespace App\Http\Controllers\v1\global;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\BookingType;
use App\Models\Coupon;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomBookingTemp;
use App\Models\RoomTypes;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\v1\hotel\HotelController;

class BookingController extends Controller
{
    use HttpResponse;

    // used in functions to check if the booking is valid
    // functionality not implemented yet!
    public function isBookingValid(
        $hotel_id,
        $check_in,
        $check_out,
        $check_in_time,
        $stayTime,
        $booking_type,
        $coupon_code,
        $rooms
    ): bool {
        if ($booking_type == 1) {
            // check if the booking is valid
            $hotel = Hotel::where('id', $hotel_id)->first();
            $hotel_availability = $hotel->hotel_available_time;
            $hotel_check_in = Carbon::parse($hotel_availability->start_time);
            $hotel_check_out = Carbon::parse($hotel_availability->end_time);

            $check_in = Carbon::parse($check_in);
            $check_out = Carbon::parse($check_out);

            if ($check_in->isBefore($hotel_check_in) || $check_out->isAfter($hotel_check_out)) {
                return false;
            }

            // check if the rooms are available
            foreach ($rooms as $room) {
                $room = Room::where('id', $room['room_id'])->first();
                if ($room->is_active == 0) {
                    return false;
                }
                // checking is room not full for booking
                $bookedRooms = BookedRoom::where('room_id', $room->id)->get();
                $allRooms = $room->room_count;
                // temp rooms
                $tempRooms = RoomBookingTemp::where('room_id', $room->id)
                    ->where('user_id', "!=", auth()->user()->id)
                    ->where('check_in', $check_in)
                    ->where('check_out', $check_out)
                    ->get();
                $totalRooms = 0;

                foreach ($bookedRooms as $bookedRoom) {
                    $totalRooms += $bookedRoom->room_count;
                }

                foreach ($tempRooms as $tempRoom) {
                    $totalRooms += $tempRoom->room_count;
                }

                if ($totalRooms + $room->room_count > $allRooms) {
                    return false;
                }
            }

            return true;
        } else if ($booking_type == 2) {
            // check if the booking is valid
            $hotel = Hotel::where('id', $hotel_id)->first();
            $hotel_availability = $hotel->hotel_available_time;
            $hotel_check_in = Carbon::parse($hotel_availability->start_time);
            $hotel_check_out = Carbon::parse($hotel_availability->end_time);

            $check_in = Carbon::parse($check_in);
            $check_out = Carbon::parse($check_out);

            if ($check_in->isBefore($hotel_check_in) || $check_out->isAfter($hotel_check_out)) {
                return false;
            }

            // check if the rooms are available
            foreach ($rooms as $room) {
                $room = Room::where('id', $room['room_id'])->first();
                if ($room->is_active == 0) {
                    return false;
                }
            }

            return true;
        }
        return false;
    }


    public function createPayOnPropertyBooking(Request $request)
    {
        try {
            $validated = $request->validate([
                'userDetails' => 'required',
                'rooms' => 'required',
                'hotel' => 'required',
                'bookingDetails' => 'required',
                'checkInTime' => 'nullable',
                'coupon_code' => 'nullable|string',
                'guest_name' => 'nullable|string'
            ]);

            // auth user can do max 3 bookings in a day
            $userBookings = Booking::where('user_id', Auth::user()->id)
                ->where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now()->endOfDay())
                ->get();

            if (count($userBookings) >= 3) {
                return $this->error(
                    message: 'You can only do 3 bookings in 1 day in PayAtProperty',
                    code: 400
                );
            }

            $userDetails = json_decode($request->userDetails);
            $bookingDetails = json_decode($request->bookingDetails);
            $roomsInfo = json_decode($request->rooms);

            //calculating payment
            $newRequestInfo = [
                'rooms' => $request->rooms,
                'check_in' => $bookingDetails->check_in_date,
                'check_out' => $bookingDetails->check_out_date,
                'stay_time' => $bookingDetails->check_in_hours,
                'booking_type' => $bookingDetails->booking_type,
                'coupon_code' => $request->coupon_code ?? ''
            ];

            $newRequest = new Request($newRequestInfo);

            $getHotelPrice = new HotelController();
            $stayPrice = $getHotelPrice->calculateAllRoomsPrice($newRequest);

            $stayPrice = ($stayPrice->getData())->data;

            if ($stayPrice->discounted_total_charge > 3000) {
                return $this->error(
                    message: 'PayAtProperty is not available for bookings above 3000',
                    code: 400
                );
            }

            //creating booking
            $booking = new Booking();
            $booking->user_id = Auth::user()->id;
            $booking->hotel_id = Room::where('id', $roomsInfo[0]->id)->first()->hotel_id;
            $booking->name = $userDetails->name;
            $booking->guest_name = $request->guest_name ?? "";
            $booking->email = $userDetails->email;
            $booking->primary_phone_number = $userDetails->primary_number;
            $booking->secondary_phone_number = $userDetails->secondary_number;
            $booking->booking_type = $bookingDetails->booking_type;
            $booking->check_in_hours = $bookingDetails->check_in_hours ?? null;

            //adding all amounts
            $booking->room_rate = $stayPrice->room_rate;
            $booking->extra_guest_charge = $stayPrice->extra_guest_charge ?? 0;
            $booking->platform_fee = $stayPrice->platform_fee ?? 0;
            $booking->convenience_fee = $stayPrice->convenience_fee ?? 0;
            $booking->amount = $stayPrice->discounted_total_charge ?? 0; // Booking amount
            if ($stayPrice->discounted_total_charge != $stayPrice->total_charge) {
                $couponId = Coupon::where('code', $request->coupon_code)->first()->id;
                if ($couponId)
                    $booking->coupon_id = $couponId;
            }
            $booking->check_in = Carbon::createFromDate($bookingDetails->check_in_date);
            $booking->payment_status = "pending"; // Payment status pending
            $booking->payment_method = "offline";
            $booking->notes = $userDetails->notes; // Payment status pending
            $booking->check_out = $bookingDetails->check_out_date ? Carbon::createFromDate($bookingDetails->check_out_date) : Carbon::now()->addDay();

            //splitting checin time into hours and minutes
            if ($request->checkInTime) {
                $booking->check_in_time = Carbon::createFromFormat('H:i:s', json_decode($validated['checkInTime']));
            }

            $booking->save();

            //saving rooms
            foreach ($roomsInfo as $room) {
                $roomDB = new BookedRoom();
                $roomDB->booking_id = $booking->id;
                $roomDB->room_id = $room->id;
                $roomDB->room_count = $room->number_of_room ?? 1;
                $roomDB->guest_count = $room->num_guest ?? 1;
                $roomDB->save();
            }

            //deleting temp booking
            foreach ($roomsInfo as $room) {
                RoomBookingTemp::where('room_id', $room->id)->where('user_id', Auth::user()->id)->delete();
            }

            return $this->success(
                message: 'Room Successfully Booked!'
            );

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getBookingDetails($booking_id)
    {
        try {
            $booking = Booking::where('booking_id', $booking_id)->first();

            $hotel = $booking->hotel;
            $booking->hotelDetails = json_encode([
                'id' => $hotel->id,
                'name' => $hotel->name,
                'slug' => $hotel->slug,
                'primary_number' => $hotel->primary_number,
                'secondary_number' => $hotel->secondary_number,
                'primary_email' => $hotel->secondary_email,
                'secondary_email' => $hotel->secondary_email,
                'image' => $hotel->banner_image,
                'address' => $hotel->address,
                'map' => $hotel->location_iframe,
            ]);

            $user = $booking->user;
            $booking->userDetails = json_encode([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
            ]);
            $hotel_availability = $booking->hotel->hotel_available_time;
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

            $updatedRooms = [];
            foreach ($rooms as $room) {
                $room_data = Room::where('id', $room['room_id'])->first();
                $room_name = RoomTypes::where('id', $room_data->room_type_id)->first()->name;

                $updatedRooms[] = [
                    'name' => $room_name,
                    'guest_count' => $room['guest_count'],
                    'room_count' => $room['room_count'],
                ];
            }

            $booking->roomDetails = json_encode($updatedRooms);

            $booking->hotelAvailability = json_encode([
                'time_in' => Carbon::parse($hotel_availability->start_time)->format('h:i A'),
                'time_out' => Carbon::parse($hotel_availability->end_time)->format('h:i A')
            ]);

            //get coupon code from coupon id
            if ($booking->coupon_id) {
                $booking->coupon_code = Coupon::where('id', $booking->coupon_id)->first()->code;
            }


            if ($booking) {
                return $this->success(
                    data: $booking,
                );
            }

            return $this->notFound(
                message: 'Booking not found'
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getBookingTypes(): JsonResponse
    {
        try {
            $types = BookingType::where('is_active', 1)->get();

            if ($types) {
                return $this->success(
                    data: $types,
                );
            }

            return $this->notFound(
                message: 'Booking Types not found'
            );

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getBookingTypesForHotel(): JsonResponse
    {
        try {
            $hotel_id = request()->route('hotel_id');
            $types = BookingType::where('is_active', 1)->get();

            if ($types) {
                return $this->success(
                    data: $types,
                );
            }

            return $this->notFound(
                message: 'Booking Types not found'
            );

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }
}
