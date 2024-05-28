<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\PropertyType;
use App\Models\Room;
use App\Models\RoomTypes;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use HttpResponse;

    public function getAllHotelsBookings()
    {
        try {
            $search = request()->query('search');

            $bookingType = request()->query('bookingType');
            if ($bookingType === 'upcoming')
                $bookings = Booking::where('is_cancelled', 0)->where('booking_id', 'LIKE', "%{$search}%")->where('payment_status', 'paid')->where('check_in', '>', date('Y-m-d'))->paginate();
            else if ($bookingType === 'ongoing')
                $bookings = Booking::where('is_cancelled', 0)->where('booking_id', 'LIKE', "%{$search}%")->where('payment_status', 'paid')->where('check_in', '<=', date('Y-m-d'))->where('check_out', '>=', date('Y-m-d'))->paginate();
            else if ($bookingType === 'past')
                $bookings = Booking::where('is_cancelled', 0)->where('booking_id', 'LIKE', "%{$search}%")->where('payment_status', 'paid')->where('check_out', '<', date('Y-m-d'))->paginate();
            else if ($bookingType === 'cancelled')
                $bookings = Booking::here('is_cancelled', 1)->where('booking_id', 'LIKE', "%{$search}%")->paginate();
            else {
                $bookings = Booking::where('is_cancelled', 0)->where('booking_id', 'LIKE', "%{$search}%")->where('payment_status', 'paid')->paginate();
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
            return $this->error(
                message: $e->getMessage()
            );
        }
    }


    public function deleteRoomType(Request $request)
    {
        try {

            $validated = $request->validate([
                'id' => 'required|integer',
            ]);


            $type = RoomTypes::where('id', $validated['id'])->firstOrFail();

            //deleting city image
            if ($type->image) {
                $imageService = new ImageUploadService();
                $imageService->deleteImage($type->image, '/rooms/types/');
            }

            if ($type->delete()) {
                return $this->success(
                    message: 'Room Type Successfully Deleted!'
                );
            } else {
                return $this->error(
                    message: 'Failed to delete Room Type!'
                );
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function editRoomType(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);

            $type = RoomTypes::find($validated['id']);

            $type->name = $validated['name'];
            $type->description = $validated['description'];
            if ($request->hasFile('image')) {
                $imageService = new ImageUploadService();
                $image = $imageService->updateImage($type->image, $request->file('image'), '/rooms/types/');
                $type->image = $image;
            }

            if ($type->save()) {
                return $this->success(
                    message: 'Room Type Updated Successfully!'
                );
            }

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function toggleRoomType()
    {
        try {
            $validated = request()->validate([
                'id' => 'required|integer',
            ]);

            $type = RoomTypes::find($validated['id']);
            $type->status = !$type->status;

            if ($type->save()) {
                return $this->success(
                    message: 'Room Type Status Changed Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to change Room Type Status!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getRoomTypes()
    {
        try {
            $search = request()->query('search');
            $types = RoomTypes::where('name', 'LIKE', "%{$search}%")->paginate(15);

            foreach ($types as $type) {
                //getting room count
                $type->room_count = $type->rooms()->count();
                //getting images
                $image = asset('storage/rooms/types/' . $type->image);
                $type->image = $image;
            }

            return $this->success(
                data: $types
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addRoomType(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);

            $room_type = new RoomTypes();
            $room_type->name = $validated['name'];
            $room_type->description = $validated['description'];

            if ($request->hasFile('image')) {
                $imageService = new ImageUploadService();
                $image = $imageService->uploadImage($request->file('image'), '/rooms/types/');
                $room_type->image = $image;
            } else {
                return $this->error(
                    message: 'Failed to new Room Type!'
                );
            }

            if ($room_type->save()) {
                return $this->success(
                    message: 'Room Type Added Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to new Room Type!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addPropertyType(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $type = new PropertyType();
            $type->name = $validated['name'];
            $type->description = $validated['description'];

            if ($request->hasFile('image')) {
                $imageService = new ImageUploadService();
                $image = $imageService->uploadImage($request->file('image'), '/rooms/type/');
                $type->image = $image;
            } else {
                return $this->error(
                    message: 'Failed to add new Property Type!'
                );
            }

            if ($type->save()) {
                return $this->success(
                    message: 'Property Type Added Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to add new Property Type!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
