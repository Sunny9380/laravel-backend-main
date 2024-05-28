<?php

namespace App\Http\Controllers\v1\hotel;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomHourlyRate;
use App\Models\RoomRates;
use App\Models\RoomsExtraAvailability;
use App\Models\RoomTypes;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    use HttpResponse;

    public function toggleRoomStatusByAdmin(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:rooms,id|max:200',
        ]);
        try {
            $room = Room::where('id', $validated['id'])
                ->first();

            if (!$room) {
                return $this->error(
                    message: 'Room not found!'
                );
            }

            $room->is_active = !$room->is_active;

            if ($room->update()) {
                return $this->success(
                    message: 'Room Status Updated Successfully!'
                );
            } else {
                return $this->error(
                    message: 'Failed to Update Room Status!'
                );
            }
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getHourlyRoomRates($hotel_slug)
    {
        try {
            $roomHourlyRates = Hotel::where('slug', $hotel_slug)
                ->select('id', 'name', 'slug')
                ->with('rooms')
                ->first();

            $roomsWithHourlyRates = [];
            foreach ($roomHourlyRates->rooms as $room) {
                $roomsWithHourlyRates[] = [
                    'id' => $room->id,
                    'name' => RoomTypes::where('id', $room->room_type_id)->first()->name,
                    'slug' => $room->slug,
                    'image' => asset('storage/rooms/image/' . $room->image),
                    'hourlyRates' => RoomHourlyRate::where('room_id', $room->id)->first(),
                    'hotelSlug' => $hotel_slug,
                ];
            }

            return $this->success(
                data: $roomsWithHourlyRates,
                message: 'Hourly Room Rates fetched successfully!'
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addHourlyRoomRates(Request $request)
    {
        try {
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'is_percent' => 'required',
                '_3_hr' => 'required|integer',
                '_4_hr' => 'required|integer',
                '_5_hr' => 'required|integer',
                '_6_hr' => 'required|integer',
                '_7_hr' => 'required|integer',
                '_8_hr' => 'required|integer',
                '_9_hr' => 'required|integer',
                '_10_hr' => 'required|integer',
                '_11_hr' => 'required|integer',
                '_12_hr' => 'required|integer',
            ]);

            //checking if that room already has hourly rates
            $roomHourlyRate = RoomHourlyRate::where('room_id', $validated['room_id'])->first();
            if (!$roomHourlyRate) {
                $roomHourlyRate = new RoomHourlyRate();
            }
            $roomHourlyRate->room_id = $validated['room_id'];
            $roomHourlyRate->is_percent = json_decode($validated['is_percent']) ? 1 : 0;
            $roomHourlyRate->_3_hr = $validated['_3_hr'];
            $roomHourlyRate->_4_hr = $validated['_4_hr'];
            $roomHourlyRate->_5_hr = $validated['_5_hr'];
            $roomHourlyRate->_6_hr = $validated['_6_hr'];
            $roomHourlyRate->_7_hr = $validated['_7_hr'];
            $roomHourlyRate->_8_hr = $validated['_8_hr'];
            $roomHourlyRate->_9_hr = $validated['_9_hr'];
            $roomHourlyRate->_10_hr = $validated['_10_hr'];
            $roomHourlyRate->_11_hr = $validated['_11_hr'];
            $roomHourlyRate->_12_hr = $validated['_12_hr'];

            if ($roomHourlyRate->save()) {
                return $this->success(
                    message: 'Hourly Room Rate Added Successfully!'
                );
            } else {
                return $this->error(
                    message: 'Failed to new Hourly Room Rate!'
                );
            }
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addRoomByAdmin(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'hotel_slug' => 'required|string|exists:hotels,slug',
                ]
            );

            $vendor_id = Hotel::where('slug', $validated['hotel_slug'])->first()->vendor_id;

            $request->merge(['vendor_id' => $vendor_id]);

            return $this->addRoom($request);
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addRoomByVendor(Request $request)
    {
        try {
            //adding vendor id to the request
            $request->merge(['vendor_id' => Auth::user()->getVendor()->id]);

            return $this->addRoom($request);

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addRoom(Request $request)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:500',
                'vendor_id' => 'required|exists:vendor,id',
                'room_type' => 'required|exists:room_types,id',
                'size' => 'required|string|max:200',
                'rate' => 'required|string|max:200',
                'num_of_rooms' => 'required|string|max:200',
                'meal_options' => 'required|string|max:200',
                'bed_type' => 'required|string|max:200',
                'guest_charge' => 'required|integer',
                'num_guest' => 'required|integer|max:200',
                'hotel_slug' => 'required|string',
            ]);

            $hotel = Hotel::where('slug', $validated['hotel_slug'])
                ->where('vendor_id', $validated['vendor_id'])
                ->first();

            if (!$hotel) {
                return $this->error(
                    message: 'Hotel not found!'
                );
            }
            $room = new Room();

            $room->hotel_id = $hotel->id;
            $room->room_type_id = $validated['room_type'];
            $room->meal_options = $validated['meal_options'];
            $room->room_size = $validated['size'];
            $room->bed_type = $validated['bed_type'];
            $room->default_rate = $validated['rate'];
            $room->num_of_rooms = $validated['num_of_rooms'];
            $room->num_guest = $validated['num_guest'];
            $room->guest_charge = $validated['guest_charge'];

            //saving image
            $imageService = new ImageUploadService();
            $room->image = $imageService->uploadImage($request->file('image'), '/rooms/image/');


            if ($room->save()) {
                return $this->success(
                    message: 'Room Added Successfully!'
                );
            } else {
                return $this->error(
                    message: 'Failed to new Room!'
                );
            }
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function updateRoom(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|integer|exists:rooms,id|max:200',
            'image' => 'nullable',
            'room_type' => 'required|exists:room_types,id',
            'size' => 'required|string|max:200',
            'rate' => 'required|string|max:200',
            'num_of_rooms' => 'required|string|max:200',
            'meal_options' => 'required|string|max:200',
            'guest_charge' => 'required|integer|max:200',
            'bed_type' => 'required|string|max:200',
            'num_guest' => 'required|integer|max:200',
            'hotel_slug' => 'required|string|max:200',
        ]);

        try {
            $room = Room::where('id', $validated['room_id'])->where('hotel_id', Hotel::where('slug', $validated['hotel_slug'])->where('vendor_id', Auth::user()->getVendor()->id)->first()->id)->first();

            if (!$room) {
                return $this->error(
                    message: 'Room not found!'
                );
            }
            $room->room_type_id = $validated['room_type'];
            $room->meal_options = $validated['meal_options'];
            $room->room_size = $validated['size'];
            $room->bed_type = $validated['bed_type'];
            $room->default_rate = $validated['rate'];
            $room->num_of_rooms = $validated['num_of_rooms'];
            $room->num_guest = $validated['num_guest'];
            $room->guest_charge = $validated['guest_charge'];

            //saving image
            if ($request->file('image')) {
                $imageService = new ImageUploadService();
                $room->image = $imageService->updateImage($room->image, $request->file('image'), '/rooms/image/');
            }

            if ($room->update()) {
                return $this->success(
                    message: 'Room Updated Successfully!'
                );
            } else {
                return $this->error(
                    message: 'Failed to update Room!'
                );
            }
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getRooms($hotel_slug)
    {
        return $this->getPropertyRooms($hotel_slug);
    }

    public function getPropertyRooms($slug)
    {
        try {
            $hotel = Hotel::where('slug', $slug)
                ->first();

            if (!$hotel) {
                return $this->error(
                    message: 'Hotel not found!'
                );
            }
            //fetching rooms
            $rooms = Room::with('rates')->where('hotel_id', $hotel->id)->get();

            //getting extra rooms
            foreach ($rooms as $room) {
                $room->extra_rooms = RoomsExtraAvailability::where('room_id', $room->id)->get();
                //getting room images
                $room->image = asset('storage/rooms/image/' . $room->image);
                //fetching room types name
                $room->name = RoomTypes::where('id', $room->room_type_id)->first()->name;
            }

            return $this->success(
                data: $rooms,
                message: 'Rooms fetched successfully!'
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getRoom($room_slug)
    {
        try {
            $room = Room::where('slug', $room_slug)->where('hotel_id', Hotel::where('vendor_id', Auth::user()->getVendor()->id)->first()->id)->first();

            $room->name = RoomTypes::where('id', $room->room_type_id)->first()->name;

            return $this->success(
                data: $room,
                message: 'Rooms fetched successfully!'
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getRoomAvailability($room_id)
    {
        try {
            $roomRates = RoomsExtraAvailability::where('room_id', $room_id)->get();

            $selectedDetails = [];

            foreach ($roomRates as $rate) {
                $roomRange = ['from' => $rate->start_date, 'to' => $rate->end_date];
                $selectedDetails[] = [
                    'id' => $rate->id,
                    'availability' => $rate->number_of_rooms,
                    'roomDateRange' => $roomRange
                ];
            }

            return $this->success(
                data: $selectedDetails,
                message: 'Room Availability fetched successfully!'
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addRoomAvailability(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|integer|exists:rooms,id|max:200',
            'availabilities' => 'required|string',
        ]);
        try {
            $availabilities = json_decode($validated['availabilities'], true);
            foreach ($availabilities as $availability) {
                if (isset($availability['deleted'])) {
                    $roomAvailability = RoomsExtraAvailability::where('id', $availability['id'])
                        ->where('room_id', $validated['room_id'])
                        ->first();
                    if ($roomAvailability && !$roomAvailability->delete()) {
                        return $this->error(
                            message: 'Something went wrong!'
                        );
                    }
                } else if (isset($availability['newlyAdded']) && isset($availability['availability']) && isset($availability['roomDateRange'])) {
                    //checking if the room start and end date is valid
                    if (strtotime($availability['roomDateRange']['from']) > strtotime($availability['roomDateRange']['to'])) {
                        return $this->error(
                            message: 'Invalid Date Range!'
                        );
                    }

                    $roomAvailability = RoomsExtraAvailability::where('room_id', $validated['room_id'])
                        ->where('start_date', '<=', $availability['roomDateRange']['from'])
                        ->where('end_date', '>=', $availability['roomDateRange']['to'])
                        ->get();
                    if (count($roomAvailability) > 0) {
                        return $this->error(
                            message: 'Room Rate already exists!'
                        );
                    }
                    //Adding new rooms
                    $room = new RoomsExtraAvailability();
                    $room->room_id = $validated['room_id'];
                    $room->number_of_rooms = $availability['availability'];
                    $room->start_date = Carbon::createFromDate($availability['roomDateRange']['from']);
                    $room->end_date = Carbon::createFromDate($availability['roomDateRange']['to']);

                    if (!$room->save()) {
                        return $this->error(
                            message: 'Something went wrong!'
                        );
                    }
                }
            }
            return $this->success(
                message: 'Room Availability Added Successfully!'
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }


    public function getRoomRates($room_id)
    {
        try {
            $roomRates = RoomRates::where('room_id', $room_id)->get();

            $selectedDetails = [];

            foreach ($roomRates as $rate) {
                $roomRange = ['from' => $rate->start_date, 'to' => $rate->end_date];
                $selectedDetails[] = [
                    'id' => $rate->id,
                    'rate' => $rate->rate,
                    'roomDateRange' => $roomRange
                ];
            }

            return $this->success(
                data: $selectedDetails,
                message: 'Room Rates fetched successfully!'
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addRoomRates(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|integer|exists:rooms,id|max:200',
            'rates' => 'required|string',
        ]);
        try {
            $rates = json_decode($validated['rates'], true);
            foreach ($rates as $rate) {
                if (isset($rate['deleted'])) {
                    $roomRates = RoomRates::where('id', $rate['id'])->where('room_id', $validated['room_id'])->first();
                    if ($roomRates && !$roomRates->delete()) {
                        return $this->error(
                            message: 'Something went wrong!'
                        );
                    }
                } else if (isset($rate['newlyAdded']) && isset($rate['rate']) && isset($rate['roomDateRange'])) {

                    //checking if the room start and end date is valid
                    if (strtotime($rate['roomDateRange']['from']) > strtotime($rate['roomDateRange']['to'])) {
                        return $this->error(
                            message: 'Invalid Date Range!'
                        );
                    }

                    $roomRates = RoomRates::where('room_id', $validated['room_id'])
                        ->where('start_date', '<=', $rate['roomDateRange']['from'])
                        ->where('end_date', '>=', $rate['roomDateRange']['to'])
                        ->get();
                    if (count($roomRates) > 0) {
                        return $this->error(
                            message: 'Room Rate already exists!'
                        );
                    }
                    //Adding new room rates
                    $room = new RoomRates();
                    $room->room_id = $validated['room_id'];
                    $room->rate = $rate['rate'];
                    $room->start_date = Carbon::createFromDate($rate['roomDateRange']['from']);
                    $room->end_date = Carbon::createFromDate($rate['roomDateRange']['to']);
                    if (!$room->save()) {
                        return $this->error(
                            message: 'Something went wrong!'
                        );
                    }
                }
            }
            return $this->success(
                message: 'Room Rate Added Successfully!'
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function changeHotelStatus(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string',
        ]);
        try {
            $hotel = Hotel::where('slug', $validated['slug'])->where('vendor_id', Auth::user()->getVendor()->id)->first();
            if (!$hotel) {
                return $this->error(
                    message: 'Unauthorized Access!'
                );
            }
            $hotel->isActive = !$hotel->isActive;
            if ($hotel->update()) {
                return $this->success(
                    message: 'Hotel Status Updated Successfully!'
                );
            } else {
                return $this->error(
                    message: 'Failed to Update Hotel Status!'
                );
            }
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }
}
