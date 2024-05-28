<?php

namespace App\Http\Controllers\v1\user;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\hotel\HotelController;
use App\Http\Traits\HttpResponse;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\PropertyType;
use App\Models\Room;
use App\Models\RoomBookingTemp;
use App\Models\RoomRates;
use App\Models\RoomsExtraAvailability;
use App\Models\RoomTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    use HttpResponse;

    public function getRoomTypes()
    {
        try {
            $roomTypes = RoomTypes::all();

            foreach ($roomTypes as $roomType) {
                $image = asset('storage/rooms/types/' . $roomType->image);
                $roomType->image = $image;
            }

            return $this->success(
                data: $roomTypes
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getRoomType($id)
    {
        try {
            $type = RoomTypes::find($id);

            $image = asset('storage/rooms/types/' . $type->image);
            $type->image = $image;

            return $this->success(
                data: $type
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getBookings()
    {
        try {
            $bookingType = request()->query('bookingType');
            if ($bookingType === 'upcoming')
                $bookings = Booking::where('user_id', Auth::user()->id)->where('is_cancelled', 0)->where('payment_status', 'paid')->where('check_in', '>', date('Y-m-d'))
                    ->orderBy('created_at', 'desc')
                    ->paginate();
            else if ($bookingType === 'ongoing')
                $bookings = Booking::where('user_id', Auth::user()->id)->where('is_cancelled', 0)->where('payment_status', 'paid')->where('check_in', '<=', date('Y-m-d'))->where('check_out', '>=', date('Y-m-d'))
                    ->orderBy('created_at', 'desc')
                    ->paginate();
            else if ($bookingType === 'past')
                $bookings = Booking::where('user_id', Auth::user()->id)->where('is_cancelled', 0)->where('payment_status', 'paid')->where('check_out', '<', date('Y-m-d'))
                    ->orderBy('created_at', 'desc')
                    ->paginate();
            else if ($bookingType === 'cancelled')
                $bookings = Booking::where('user_id', Auth::user()->id)->where('is_cancelled', 1)
                    ->orderBy('created_at', 'desc')
                    ->paginate();
            else {
                $bookings = Booking::where('user_id', Auth::user()->id)->where('is_cancelled', 0)->where('payment_status', 'paid')
                    ->orderBy('created_at', 'desc')
                    ->paginate();
            }

            //getting hotel info
            foreach ($bookings as $booking) {
                $hotel = Hotel::where('id', $booking->hotel_id)->first();
                $booking->hotel_image = $hotel->banner_image;
                $booking->hotel_name = $hotel->name;
                $booking->hotel_slug = $hotel->slug;
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

                foreach ($rooms as $room) {
                    $fetchedRoom = Room::where('id', $room['room_id'])->first();

                    $room['name'] = $fetchedRoom->name;
                }

                $booking->rooms = json_encode($rooms);
            }

            return $this->success(
                data: $bookings
            );
        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }

    public function getRoomTypeImage($filename)
    {
        $image = asset('storage/rooms/type/' . $filename);
        //Sending image
        return $this->success(
            data: $image
        );
    }

    public function getRoomImage($filename)
    {
        try {
            $image = asset('storage/rooms/image/' . $filename);
            //Sending image
            return $this->success(
                data: $image
            );
        } catch (\Exception $e) {
            return $this->internalError(
                message: $e->getMessage()
            );
        }
    }

    public function propertyTypeAvailableProperties($slug)
    {
        try {
            $search = request()->query('search');
            $property_type = PropertyType::where('slug', $slug)->first();
            $properties = Hotel::with('rooms')
                ->select('id', 'name', 'slug', 'description', 'property_type_id', 'banner_image', 'address', 'city_id', 'country', 'zip')
                ->where('property_type_id', $property_type->id)
                ->where('name', 'LIKE', "%{$search}%")
                ->with('amenities')
                ->where('isActive', 1)
                ->where('isVerified', 1)
                ->where('isBanned', 0)
                ->paginate(15);

            //finding the lowest rate between rooms
            $hotelController = new HotelController();
            foreach ($properties as $key => $property) {
                $lowest_rate = $hotelController->getPropertyLowestRate($property);
                $property->lowest_rate = $lowest_rate;
                $property->property_type = PropertyType::where('id', $property->property_type_id)->first()->name;
                $property->banner_image = asset('storage/hotels/banner_image/' . $property->banner_image);
            }


            return $this->success(
                data: $properties
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function propertyTypeAvailablePropertiesCount($slug)
    {
        try {
            $property_type = PropertyType::where('slug', $slug)->first();
            $propertiesCount = Hotel::where('property_type_id', $property_type->id)->count();
            return $this->success(
                data: $propertiesCount
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getBookedRooms()
    {
        try {
            $booking = Booking::where('user_id', Auth::user()->id)->get();
            return $this->success(
                data: $booking
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getRoomsDetails($property_id, $checkIn, $checkOut, $stayTime, $bookingType)
    {
        //getting all the room details
        $rooms = Room::where('hotel_id', $property_id)
            ->where('is_active', 1)
            ->get();

        if (!$rooms->count()) {
            return $this->error(
                message: 'No rooms found'
            );
        }

        foreach ($rooms as $key => $room) {
            //0 because we don't want to book here
            $roomAvailability = $this->getRoomDetailsForBooking(
                $room->id,
                0,
                $checkIn,
                $checkOut
            )->getData()->data;

            if ($roomAvailability) {
                $room->availability = $roomAvailability->availability;
            } else {
                $room->availability = 0;
            }
            //getting hotel type name
            $room->name = RoomTypes::where('id', $room->room_type_id)
                ->first()->name;

            $room->image = asset('storage/rooms/image/' . $room->image);

            $room_current_rate = 0;
            //getting current rate of room according to today's date
            if ($bookingType == 1) {
                $room_current_rate = RoomRates::where('room_id', $room->id)
                    ->where('start_date', '<=', $checkIn)
                    ->where('end_date', '>=', $checkOut)
                    ->first();
                if ($room_current_rate) {
                    $room->current_rate = $room_current_rate->rate;
                } else {
                    $room->current_rate = null;
                }
            } else {
                $calclateStayPriceReq = new Request([
                    'room_type_id' => $room->id,
                    'stay_time' => $stayTime,
                    'check_in' => $checkIn,
                ]);

                $hotelController = new HotelController();
                $room_current_rate = $hotelController->calculateRoomHourlyStayPrice($calclateStayPriceReq)->getData()->data;

                if ($room_current_rate) {
                    $room->current_rate = $room_current_rate;
                    $room->default_rate = $room_current_rate;
                } else {
                    $room->current_rate = null;
                }
            }
        }

        return $this->success(
            data: $rooms
        );
    }

    private function getRoomDetailsForBooking($room_id, $num_of_rooms_needed = 1, $check_in, $check_out)
    {
        // Retrieve room details
        $room = Room::where('id', $room_id)
            ->where('is_active', 1)
            ->first();
        $now = Carbon::now();

        //checking bookings
        $property = Hotel::where('id', $room->hotel_id)->first();
        $hotelBookings = Booking::where('hotel_id', $property->id)
            ->where('is_cancelled', 0)
            ->with('bookedRooms')
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere('payment_status', 'onSite');
            })
            ->where(function ($query) use ($check_in, $check_out) {
                $query->where(function ($q) use ($check_in, $check_out) {
                    $q->whereDate('check_in', '>=', $check_in)
                        ->whereDate('check_out', '<=', $check_out);
                })->orWhere(function ($q) use ($check_in, $check_out) {
                    $q->whereDate('check_in', '<=', $check_in)
                        ->whereDate('check_out', '>=', $check_in);
                })->orWhere(function ($q) use ($check_in, $check_out) {
                    $q->whereDate('check_in', '<=', $check_out)
                        ->whereDate('check_out', '>=', $check_out);
                });
            })
            ->get();

        // adding room extra availability
        $roomExtraAvailability = RoomsExtraAvailability::where('room_id', $room_id)
            ->where('start_date', '<=', $check_out)
            ->where('end_date', '>=', $check_in)
            ->sum('number_of_rooms');

        $room->num_of_rooms += $roomExtraAvailability;

        // Calculate total booked rooms
        $bookedRoomsCount = 0;
        foreach ($hotelBookings as $booking) {
            foreach ($booking->bookedRooms as $bookedRoom) {
                if ($bookedRoom->room_id == $room_id) {
                    $bookedRoomsCount += $bookedRoom->room_count;
                }
            }
        }

        // Calculate available rooms after considering booked rooms
        $totalRoomAvailable = $room->num_of_rooms - $bookedRoomsCount;

        // Check if room is fully booked
        if ($totalRoomAvailable < $num_of_rooms_needed) {
            return $this->error(
                message: 'Room is fully booked'
            );
        }


        // Adjust available rooms considering temporary bookings in the last 15 minutes
        $tempBookingsCount = RoomBookingTemp::where('room_id', $room_id)
            ->whereDate('created_at', $now->toDateString()) // Check if the date is today
            ->whereTime('created_at', '>', $now->subMinutes(15)->toTimeString()) // Check if the time is more than 15 minutes ago
            ->sum('room_count');

        $totalRoomAvailable -= $tempBookingsCount;

        // Check if room is still available after considering temporary bookings
        if ($totalRoomAvailable < $num_of_rooms_needed) {
            // Check if the user has additional rooms from temporary bookings
            $tempBooking = RoomBookingTemp::where('room_id', $room_id)
                ->where('user_id', Auth::check() ? Auth::user()->id : null)
                ->whereDate('created_at', $now->toDateString()) // Check if the date is today
                ->whereTime('created_at', '>', $now->subMinutes(15)->toTimeString()) // Check if the time is more than 15 minutes ago
                ->sum('room_count');

            $totalRoomAvailable += $tempBooking;

            // Final check for available rooms
            if ($totalRoomAvailable < $num_of_rooms_needed) {
                return $this->error(
                    message: 'Room is fully booked'
                );
            }
        }

        // Update room availability and return success response
        $room->availability = $totalRoomAvailable - $num_of_rooms_needed;

        return $this->success($room);
    }


    private function isRoomIsInBookingTemp($room_id)
    {
        $room = RoomBookingTemp::where('user_id', Auth::user()->id)->where('room_id', $room_id)->first();
        if ($room) {
            return true;
        }
        return false;
    }


    public function getHotelDetailsForBooking(Request $request)
    {
        try {
            $validated = $request->validate([
                'hotel_slug' => 'required|string',
                'rooms' => 'required|string',
                'coupon_id' => 'nullable|string',
            ]);
            $hotel = Hotel::where('slug', $validated['hotel_slug'])->first();

            $rooms_data = json_decode($validated['rooms']);
            $images = [];
            $roomsDetails = null;

            foreach ($rooms_data as $room) {
                $available_room = $this->getRoomDetailsForBooking(
                    $room->id,
                    $room->number_of_room,
                    date('Y-m-d'),
                    date('Y-m-d', strtotime('+1 day'))
                )->getData();
                //storing room on 15min hold
                //deleting previous temp booking
                RoomBookingTemp::where('room_id', $room->id)
                    ->where('user_id', Auth::user()->id)
                    ->delete();
                //adding new temp booking
                $roomBookingTemp = new RoomBookingTemp();
                $roomBookingTemp->user_id = Auth::user()->id;
                $roomBookingTemp->room_id = $room->id;
                $roomBookingTemp->room_count = $room->number_of_room;
                $roomBookingTemp->save();

                //checking response
                if ($available_room->data == null) {
                    return $this->error(
                        message: $available_room->message
                    );
                }
                $roomData = $available_room->data;
                $room_type = RoomTypes::where('id', $roomData->room_type_id)->first();
                $room_name[] = $room_type->name;
                $roomsDetails[] = [
                    'room_name' => $room_type->name,
                    'room_count' => $room->number_of_room,
                ];
                $images[] = asset('storage/rooms/image/' . $roomData->image);
            }
            $details = [
                'images' => $images,
                'rooms_details' => $roomsDetails,
                'hotel_name' => $hotel->name,
                'pay_in_property' => $hotel->pay_in_property,
            ];
            return $this->success(
                data: $details
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }
}
