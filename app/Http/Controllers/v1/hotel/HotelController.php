<?php

namespace App\Http\Controllers\v1\hotel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\global\CouponController;
use App\Http\Controllers\v1\user\RoomController;
use App\Http\Requests\Hotel\HotelRequest;
use App\Http\Traits\HttpResponse;
use App\Jobs\MailVerifiedEmail;
use App\Jobs\vendor\WelcomeVendorMail;
use App\Models\Amenity;
use App\Models\BookingType;
use App\Models\City;
use App\Models\Configuration;
use App\Models\Hotel;
use App\Models\HotelGallery;
use App\Models\HotelTypeBookingOptions;
use App\Models\Policies;
use App\Models\PropertyAmenities;
use App\Models\PropertyAvailabilityTime;
use App\Models\PropertyRequest;
use App\Models\PropertyType;
use App\Models\RequestVerifiedMail;
use App\Models\Room;
use App\Models\RoomHourlyRate;
use App\Models\RoomRates;
use App\Models\RoomsExtraAvailability;
use App\Models\RoomTypes;
use App\Models\State;
use App\Models\User;
use App\Models\Vendor;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    use HttpResponse;

    public function togglePropertyStatus($slug)
    {
        try {
            $property = Hotel::where('slug', $slug)->firstOrFail();

            $property->isActive = !$property->isActive;

            if ($property->save()) {
                $message = $property->isActive ? 'Property Activated Successfully!' : 'Property Deactivated Successfully!';
                return $this->success(message: $message);
            }

            return $this->error(message: 'Failed to toggle property activation status.');
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deleteVendorRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:list_property_requests,id',
            ],
            [
                'id.exists' => 'The selected property request does not exist.',
            ]);

            $propertyMailList = PropertyRequest::findOrFail($validated['id']);

            if ($propertyMailList->delete()) {
                return $this->success(message: 'Request Deleted Successfully!');
            }

            return $this->error(message: 'Failed to delete property request.');
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getAllHotels()
    {
        try {
            $vendorId = Auth::user()->vendor->id;

            $hotels = Hotel::where('vendor_id', $vendorId)
                ->select('id', 'name')
                ->get();

            return $this->success(data: $hotels);
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function verifyVendorRequest(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'id' => 'required|exists:list_property_requests,id',
                    'commission' => 'required|numeric'
                ]
            );

            //creating new user
            $propertyMailList = PropertyRequest::where('id', $validated['id'])->first();

            if (!$propertyMailList) {
                return $this->error(
                    message: 'Request not found!'
                );
            }

            //checking if the email already exists
            $user = User::where('email', $propertyMailList->email)->first();
            if ($user) {
                //promotimg user to vendor
                $user->role = 1;
                $user->password = bcrypt($propertyMailList->password);
                $user->update();
                //creating vendor out of it
                $vendor = Vendor::where('user_id', $user->id)->first();
                if ($vendor) {
                    return $this->error(
                        message: 'This user is already a vendor!'
                    );
                }
                $vendor = new Vendor();
                $vendor->user_id = $user->id;
                //Generating Unique Vendor ID
                $vendor_id = null;
                do {
                    $vendor_id = 'V-' . substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6);
                } while (Vendor::where('vendor_id', $vendor_id)->exists());

                $vendor->vendor_id = $vendor_id;
                $vendor->email = $propertyMailList->email;
                $vendor->name = $propertyMailList->name;
                $vendor->phone_number = $propertyMailList->phone;
                $vendor->address = $propertyMailList->address;
                $vendor->gst_number = $propertyMailList->gst_number;
                $vendor->commission = $validated['commission'];
                $propertyMailList->delete();
                if ($vendor->save()) {
                    $vendorData = [
                        'vendor_id' => $vendor_id,
                        'email' => $vendor->email,
                        'password' => $propertyMailList->password,
                    ];
                    //sending mail to vendor
                    dispatch(new WelcomeVendorMail($vendorData));

                    return $this->success(
                        message: 'Vendor Created Successfully!'
                    );
                }
            } else {
                $user = new User();
                $user->name = $propertyMailList->name;
                $user->email = $propertyMailList->email;
                $user->password = bcrypt('password');
                $user->role = 1;
                $user->save();

                $vendor = new Vendor();
                $vendor->user_id = $user->id;
                //Generating Unique Vendor ID
                $vendor_id = null;
                do {
                    $vendor_id = 'V-' . substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6);
                } while (Vendor::where('vendor_id', $vendor_id)->exists());
                $vendor->vendor_id = $vendor_id;
                $vendor->email = $propertyMailList->email;
                $vendor->name = $propertyMailList->name;
                $vendor->phone_number = $propertyMailList->phone;
                $vendor->address = $propertyMailList->address;
                $vendor->gst_number = $propertyMailList->gst_number;
                $vendor->commission = $validated['commission'];
                $propertyMailList->delete();
                if ($vendor->save()) {
                    $vendorData = [
                        'vendor_id' => $vendor_id,
                        'email' => $vendor->email,
                        'password' => $vendor->password,
                    ];
                    //sending mail to vendor
                    dispatch(new WelcomeVendorMail($vendorData));


                    return $this->success(
                        message: 'Vendor Created Successfully!'
                    );
                }
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function verifyVendorRequestsMail(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'id' => 'required|exists:request_verified_mail,id'
                ]
            );

            $propertyMailList = RequestVerifiedMail::where('id', $validated['id'])->first();

            if (!$propertyMailList) {
                return $this->error(
                    message: 'Request not found!'
                );
            }

            $propertyMailList->verified_at = now();

            if ($propertyMailList->update()) {
                dispatch(new MailVerifiedEmail([
                    'email' => $propertyMailList->email
                ]));

                return $this->success(
                    message: 'Request Mail Verified successfully!'
                );
            }

            return $this->error(
                message: 'Failed to verify Request Mail!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deleteVendorRequestsMail(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'id' => 'required|exists:request_verified_mail,id'
                ]
            );

            $propertyMailList = RequestVerifiedMail::where('id', $validated['id'])->first();

            if ($propertyMailList->delete()) {
                return $this->success(
                    message: 'Request Mail Deleted Successfully!'
                );
            }

            return $this->error(
                message: 'Failed to delete Request Mail!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }


    public function vendorRequestsMails()
    {
        try {

            $search = request()->query('search');
            $propertyMailList = RequestVerifiedMail::where('email', 'LIKE', "%{$search}%")
                ->paginate(15);

            foreach ($propertyMailList as $propertyMail) {
                $user = User::where('id', $propertyMail->user_id)->first();
                $propertyMail->user_name = $user->name;
                $propertyMail->user_email = $user->email;
            }

            return $this->success(
                data: $propertyMailList
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function allVendorRequests()
    {
        try {

            $search = request()->query('search');
            $propertyListRequest = PropertyRequest::where('name', 'LIKE', "%{$search}%")
                ->paginate(15);

            return $this->success(
                data: $propertyListRequest
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function unBanProperty($slug)
    {
        try {
            $property = Hotel::where('slug', $slug)->first();

            $property->isBanned = 0;

            if ($property->update()) {
                return $this->success(
                    message: 'Ban From Property Removed Successfully!'
                );
            }

            return $this->error(
                message: 'Failed to remove Ban from property!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }
    public function banProperty($slug)
    {
        try {
            $property = Hotel::where('slug', $slug)->first();

            $property->isBanned = 1;

            if ($property->update()) {
                return $this->success(
                    message: 'Property Banned Successfully!'
                );
            }

            return $this->error(
                message: 'Failed to Ban property!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function verifyProperty($slug)
    {
        try {
            $property = Hotel::where('slug', $slug)->first();

            $property->isVerified = 1;

            if ($property->update()) {
                return $this->success(
                    message: 'Property Verified Successfully!'
                );
            }

            return $this->error(
                message: 'Failed to verify property!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getPropertyDetails($slug)
    {
        try {
            $property = Hotel::where('slug', $slug)
                ->with('getVendor')
                ->with('amenities')
                ->first();

            $property->city = City::where('id', $property->city_id)->first()->name;
            $property->property_type = PropertyType::where('id', $property->property_type_id)->first()->name;

            $property->banner_image = asset('storage/hotels/banner_image/' . $property->banner_image);

            $hotel_gallery = HotelGallery::where('hotel_id', $property->id)->get();

            foreach ($hotel_gallery as $key => $image) {
                $property->gallery .= asset('storage/hotels/gallery/' . $image->image) . ',';
            }

            $tenancyAggrement = '';
            foreach (json_decode($property->tenancy_agreement) as $key => $image) {
                $tenancyAggrement = $tenancyAggrement . ',' . asset('storage/hotels/tenancy_agreement/' . $image);
            }
            $property->tenancy_agreement = $tenancyAggrement;

            $corporate_documents = '';
            foreach (json_decode($property->corporate_documents) as $key => $image) {
                $corporate_documents = $corporate_documents . ',' . asset('storage/hotels/corporate_documents/' . $image);
            }
            $property->corporate_documents = $corporate_documents;

            $identity_documents = '';
            foreach (json_decode($property->identity_documents) as $key => $image) {
                $identity_documents = $tenancyAggrement . ',' . asset('storage/hotels/identity_documents/' . $image);
            }
            $property->identity_documents = $identity_documents;

            $proof_of_ownership = '';
            foreach (json_decode($property->proof_of_ownership) as $key => $image) {
                $proof_of_ownership = $proof_of_ownership . ',' . asset('storage/hotels/proof_of_ownership/' . $image);
            }
            $property->proof_of_ownership = $proof_of_ownership;

            $property->vendor_image = asset('storage/user/image/' . $property->getVendor->more_info->image);

            return $this->success(
                data: $property
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getPropertiesRequestList()
    {
        try {
            $search = request()->query('search');
            $properties = Hotel::where('name', 'LIKE', "%{$search}%")
                ->with('getBookingType')
                ->with('rooms')
                ->with('getVendor')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            foreach ($properties as $key => $property) {
                $property->property_type = PropertyType::where('id', $property->property_type_id)->first()->name;
                $property->availability = PropertyAvailabilityTime::where('property_id', $property->id)->first();
                $property->banner_image = asset('storage/hotels/banner_image/' . $property->banner_image);
                if ($property->getVendor->image) {
                    $property->getVendor->image = asset('storage/user/image/' . $property->getVendor->more_info->image);
                }
            }

            return $this->success(
                data: $properties
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }
    public function getAmenity($id)
    {
        try {
            $amenity = Amenity::where('id', $id)->first();
            return $this->success(
                data: $amenity,
                message: $id
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deleteAmenity(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(
                [
                    'id' => 'required|exists:amenities,id'
                ]
            );

            $amenity = Amenity::where('id', $validated['id'])->first();

            if ($amenity->delete()) {
                return $this->success(
                    message: 'Amenity Successfully Deleted!'
                );
            } else {
                return $this->error(
                    message: 'Failed to delete Amenity!'
                );
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getAmenities()
    {
        try {
            $search = request()->query('search');
            $amenities = Amenity::where('name', 'LIKE', "%{$search}%")
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($amenities as $key => $amenity) {
                //getting no of hotels using this amenity
                $amenity->count = PropertyAmenities::where('amenity_id', $amenity->id)->count();
            }

            return $this->success(
                data: $amenities
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function updateAmenity(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'id' => 'required|exists:amenities,id',
                    'name' => 'required|string|max:255',
                    'icon' => 'required|string|max:255',
                    'is_special' => 'required|boolean',
                ]
            );

            $amenity = Amenity::where('id', $validated['id'])->first();
            $amenity->name = $validated['name'];
            $amenity->icon = $validated['icon'];
            $amenity->is_special = $validated['is_special'];

            if ($amenity->update()) {
                return $this->success(
                    message: 'Amenity Updated Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to update Amenity!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addAmenity(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'name' => 'required|string|max:255',
                    'icon' => 'required|string|max:255',
                    'is_special' => 'required|boolean',
                ]
            );

            $amenity = new Amenity();
            $amenity->name = $validated['name'];
            $amenity->icon = $validated['icon'];
            $amenity->is_special = $validated['is_special'];
            if ($amenity->save()) {
                return $this->success(
                    message: 'Amenity Added Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to add Amenity!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getPropertiesDataForSearch()
    {
        try {
            $selectedPropertySlug = request()->query('selectedProperty');

            $properties = Hotel::where('isActive', 1)
                ->where('isVerified', 1)
                ->where('isBanned', 0)
                ->select('id', 'name', 'slug', 'city_id', 'property_type_id')
                ->get();

            $selectedProperty = Hotel::where('slug', $selectedPropertySlug)
                ->where('isActive', 1)
                ->where('isVerified', 1)
                ->where('isBanned', 0)
                ->select('name', 'city_id')
                ->first();

            $selectedProperty->city = City::where('id', $selectedProperty->city_id)
                ->first()
                ->name;

            //getting city name
            foreach ($properties as $key => $property) {
                $property->city = City::where('id', $property->city_id)->first()->name;
                $property->property_type = PropertyType::where('id', $property->property_type_id)->first()->name;
                if ($property->slug == $selectedPropertySlug) {
                    $property->selected = true;
                }
            }

            $booking_type = BookingType::where('is_active', 1)
                ->select('id', 'name')
                ->get();

            $booking_type[0]->selected = true;

            return $this->success([
                'booking_types' => $booking_type,
                'properties' => $properties,
                'selectedProperty' => $selectedProperty
            ]);
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getDataForSearch()
    {
        try {
            $states = State::where('is_stopped', 0)->get();
            //setting cities
            $cities = [];
            foreach ($states as $key => $state) {
                foreach ($state->cities as $city) {
                    if ($city->is_stopped == 0) {
                        $cities[] = [
                            'id' => $city->id,
                            'name' => $city->name,
                        ];
                    }
                }
            }
            $booking_type = BookingType::where('is_active', 1)->select('id', 'name')->get();
            $property_type = PropertyType::where('is_active', 1)->select('id', 'name')->get();

            return $this->success([
                'cities' => $cities,
                'booking_types' => $booking_type,
                'property_types' => $property_type
            ]);
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function searchHotel(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'check_in' => 'required|date',
                    'check_out' => 'required|date',
                    'stay_time' => 'required|integer|max:12',
                    'city' => 'required|exists:cities,id',
                    'booking_type' => 'required|integer|exists:booking_types,id',
                    'property_types' => 'required',
                ]
            );

            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = Carbon::parse($validated['check_out']);

            if (
                ($validated['booking_type'] == 1 && $checkIn->greaterThanOrEqualTo($checkOut))
                || ($checkIn->lessThan(Carbon::now()))
            ) {
                return $this->error(
                    message: 'Dates are not valid!'
                );
            }

            $hotels = Hotel::join('hotel_type_booking_options', 'hotels.id', '=', 'hotel_type_booking_options.hotel_id')
                ->where('booking_type_id', $validated['booking_type'])
                ->whereIn('property_type_id', json_decode($validated['property_types']))
                ->where('city_id', $validated['city'])
                ->where('isActive', 1)
                ->where('isVerified', 1)
                ->where('isBanned', 0)
                ->select(
                    'hotels.id',
                    'hotels.name',
                    'hotels.slug',
                    'hotels.description',
                    'hotels.description',
                    'hotels.property_type_id',
                    'hotels.banner_image',
                    'hotel_type_booking_options.booking_type_id'
                )
                ->get();

            if (!$hotels->count()) {
                return $this->error(
                    message: 'No hotels found!'
                );
            }

            $availableRooms = [];

            //getting rooms available for booking
            foreach ($hotels as $key => $hotel) {
                //getting total rooms
                $rooms = Room::where('hotel_id', $hotel->id)->get();

                foreach ($rooms as $room) {

                    //finding room name
                    $room->name = RoomTypes::where('id', $room->room_type_id)->first()->name;

                    $totalRooms = $room->num_of_rooms ?? 0;
                    if ($validated['booking_type'] === 1) {
                        //calculating for overnight
                        $extraRooms = RoomsExtraAvailability::where('room_id', $room->id)
                            ->where('start_date', ">=", Carbon::parse($validated['check_in'])->toDateTimeString())
                            ->where('end_date', "<=", Carbon::parse($validated['check_out'])->toDateTimeString())
                            ->get();
                    } else {
                        $extraRooms = RoomsExtraAvailability::where('room_id', $room->id)
                            ->where('start_date', "<=", Carbon::parse($validated['check_in'])->toDateTimeString())
                            ->get();
                    }

                    foreach ($extraRooms as $extraRoom) {
                        $totalRooms += $extraRoom->number_of_rooms;
                    }

                    if ($totalRooms >= 1) {
                        $roomRequest = new Request([
                            'room_type_id' => $room->id,
                            'check_in' => $validated['check_in'],
                            'check_out' => $validated['check_out'],
                            'guests' => 1,
                            'stay_time' => $validated['stay_time'] ?? null,
                            'booking_type' => $validated['booking_type'],
                            'addFee' => false
                        ]);

                        //calculating room price
                        $room_price = $this->calculateRoomPrice($roomRequest)->getData()->data;

                        $propertyType = PropertyType::where('id', $hotel->property_type_id)->first()->name;

                        $availableRooms[] = [
                            'property_id' => $hotel->id,
                            'property_type' => $propertyType,
                            'property_name' => $hotel->name,
                            'property_slug' => $hotel->slug,
                            'image' => asset('storage/hotels/banner_image/' . $hotel->banner_image),
                            'room_id' => $room->id,
                            'room_name' => $room->name,
                            'room_slug' => $room->slug,
                            'room_rate' => $room_price->room_rate,
                        ];
                    }
                }
            }

            return $this->success(
                data: $availableRooms
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    //saving booking type in other table
    protected function addBookingType($bookingType, $hotel_id)
    {
        //Adding booking type
        $HotelBookingTypeOvernight = new HotelTypeBookingOptions();
        $HotelBookingTypeOvernight->hotel_id = $hotel_id;
        $HotelBookingTypeOvernight->booking_type_id = 1;
        $HotelBookingTypeOvernight->save();
        if ($bookingType === '2') {
            $HotelBookingTypeHourly = new HotelTypeBookingOptions();
            $HotelBookingTypeHourly->hotel_id = $hotel_id;
            $HotelBookingTypeHourly->booking_type_id = 2;
            $HotelBookingTypeHourly->save();
        }
    }

    protected function getRoomRate($room_id, $date)
    {
        $room_rate = RoomRates::where('room_id', $room_id)->first();
        if ($room_rate && $room_rate->start_date <= $date && $room_rate->end_date >= $date) {
            return $room_rate->rate;
        }
        return Room::where('id', $room_id)->first()->default_rate;
    }

    public function calculateRoomHourlyStayPrice(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'room_type_id' => 'required|integer',
                    'stay_time' => 'nullable|integer',
                    'check_in' => 'required|date',
                ]
            );
            $total_room_rates = 0;

            //checking if vendor has aded his room hourly rates
            $roomHourlyRates = RoomHourlyRate::where('room_id', $validated['room_type_id'])->first();
            // room_id	is_percent	_3_hr	_4_hr	_5_hr	_6_hr	_7_hr	_8_hr	_9_hr	_10_hr	_11_hr	_12_hr
            //now checking is the stay time between these then send the closest price
            if ($roomHourlyRates) {
                switch ($validated['stay_time']) {
                    case 3:
                        $total_room_rates = $roomHourlyRates->_3_hr;
                        break;
                    case 4:
                        $total_room_rates = $roomHourlyRates->_4_hr;
                        break;
                    case 5:
                        $total_room_rates = $roomHourlyRates->_5_hr;
                        break;
                    case 6:
                        $total_room_rates = $roomHourlyRates->_6_hr;
                        break;
                    case 7:
                        $total_room_rates = $roomHourlyRates->_7_hr;
                        break;
                    case 8:
                        $total_room_rates = $roomHourlyRates->_8_hr;
                        break;
                    case 9:
                        $total_room_rates = $roomHourlyRates->_9_hr;
                        break;
                    case 10:
                        $total_room_rates = $roomHourlyRates->_10_hr;
                        break;
                    case 11:
                        $total_room_rates = $roomHourlyRates->_11_hr;
                        break;
                    case 12:
                        $total_room_rates = $roomHourlyRates->_12_hr;
                        break;
                    default:
                        $total_room_rates = $roomHourlyRates->_3_hr;
                }

                if ($total_room_rates != 0) {
                    return $this->success(
                        data: $total_room_rates
                    );
                }
            }

            //else using default
            $room_rate = $this->getRoomRate($validated['room_type_id'], $validated['check_in']->toDateTimeString());
            if ($validated['stay_time'] <= 3) {
                $total_room_rates = round(($room_rate / 100) * 25);
            } else if ($validated['stay_time'] <= 6) {
                $total_room_rates = round(($room_rate / 100) * 50);
            } else if ($validated['stay_time'] <= 10) {
                $total_room_rates = round(($room_rate / 100) * 75);
            } else {
                $total_room_rates = $room_rate;
            }
            return $this->success(
                data: $total_room_rates
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function calculateRoomPrice(Request $request)
    {
        $validated = $request->validate(
            [
                'room_type_id' => 'required|integer', //this is id of room
                'check_in' => 'required|date',
                'check_out' => 'nullable|date',
                'guests' => 'required|integer',
                'addFee' => 'nullable|boolean',
                'stay_time' => 'nullable|integer',
                'booking_type' => 'required|integer|exists:booking_types,id',
            ]
        );
        try {
            $room = Room::where('id', $validated['room_type_id'])->first();

            if (!$room) {
                return $this->error(
                    message: 'Room not found!'
                );
            }

            $check_in = Carbon::parse($validated['check_in']);
            $check_out = Carbon::parse($validated['check_out']);
            $total_charge = 0;
            $total_room_rates = 0;

            //booking type is hourly
            if ($validated['booking_type'] == 2) {
                //calculating all hours room rates by looping through each hour
                $hours = $validated['stay_time'];

                $calclateStayPriceReq = new Request([
                    'room_type_id' => $validated['room_type_id'],
                    'stay_time' => $hours,
                    'check_in' => $check_in,
                ]);

                $total_room_rates = $this->calculateRoomHourlyStayPrice($calclateStayPriceReq)->getData()->data;
            } else if ($validated['booking_type'] == 1) {
                //if booking type is overnight
                //checking date
                if ($check_in->greaterThanOrEqualTo($check_out)) {
                    return $this->error(
                        message: 'Check In date cannot be greater than Check Out date!'
                    );
                }
                //calculating all days room rates by looping through each day
                $days = $check_in->diffInDays($check_out);
                for ($i = 0; $i < $days; $i++) {
                    $total_room_rates += $this->getRoomRate($validated['room_type_id'], $check_in->addDays($i)->toDateTimeString());
                }
            }

            $total_charge += $total_room_rates;

            //checking guest
            $allowed_guest = $room->num_guest;
            $extra_guest = $validated['guests'] - $allowed_guest;

            $guest_charge = $room->guest_charge;
            $extra_guest_charge = 0;
            if ($extra_guest > 0) {
                $extra_guest_charge = $extra_guest * $guest_charge;
            }

            $total_charge += $extra_guest_charge;

            //Adding extra fees
            $fee_structure = [];
            if ($validated['addFee']) {
            } else {
                $fee_structure = [
                    'room_rate' => $total_room_rates,
                    'extra_guest_charge' => $extra_guest_charge,
                ];
            }

            return $this->success(
                data: $fee_structure
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function calculateAllRoomsPrice(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'rooms' => 'required|string',
                    'check_in' => 'required|date',
                    'check_out' => 'nullable|date',
                    'stay_time' => 'nullable|integer',
                    'booking_type' => 'required|integer|exists:booking_types,id',
                    'coupon_code' => 'nullable|string'
                ]
            );

            $total_charge = 0;
            $discounted_total_charge = 0;
            $extra_guest_charge = 0;
            $room_rate = 0;

            $property_id = null;

            //calculating all rooms price one by one
            foreach (json_decode($validated['rooms']) as $key => $room) {

                if (!$room)
                    continue;

                if ($property_id == null) {
                    $property_id = Room::where('id', $room->room_type_id ?? $room->id ?? $room->room_id)->first()->hotel_id;
                }

                $roomRequest = new Request([
                    'room_type_id' => $room->room_type_id ?? $room->id ?? $room->room_id,
                    'check_in' => $validated['check_in'],
                    'check_out' => $validated['check_out'],
                    'guests' => $room->number_of_guest ?? $room->guest_count,
                    'stay_time' => $validated['stay_time'] ?? null,
                    'booking_type' => $validated['booking_type'],
                    'addFee' => false
                ]);
                //calculating room price
                $room_price = $this->calculateRoomPrice($roomRequest)->getData()->data;

                $extra_guest_charge += $room_price->extra_guest_charge;

                $room_rate += ($room_price->room_rate * ($room->number_of_room ?? $room->room_count ?? 1));

                $total_charge += $room_rate + $room_price->extra_guest_charge;
            }

            if ($total_charge == 0) {
                return $this->success(
                    data: [
                        'room_rate' => 0,
                        'extra_guest_charge' => 0,
                        'platform_fee' => 0,
                        'convenience_fee' => 0,
                        'total_charge' => 0,
                        'discounted_price' => 0
                    ]
                );
            }

            $couponMessage = null;

            //applying discount if coupon code is available
            if ($request->coupon_code && $validated['coupon_code']) {
                if ($property_id == null)
                    return $this->error(
                        message: 'Property not found!'
                    );

                $couponController = new CouponController();
                //creating new form request
                $couponRequest = new Request([
                    'coupon_code' => $validated['coupon_code'],
                    'price' => $total_charge,
                    'property_id' => $property_id
                ]);

                //checking coupon response is 200 then send response
                $couponResponse = $couponController->getCouponDiscountedPrice($couponRequest);
                $couponMessage = $couponResponse->getData()->message;
                if ($couponResponse->status() === 200) {
                    $discounted_total_charge += $couponResponse->getData()->data;
                } else {
                    $discounted_total_charge = $total_charge;
                }
            }

            //Adding extra fees
            $fees = Configuration::first();

            $total_charge += $fees->platform_fee + $fees->convenience_fee;
            if ($discounted_total_charge == 0) {
                $discounted_total_charge = $total_charge;
            } else {
                $discounted_total_charge += $fees->platform_fee + $fees->convenience_fee;
            }

            return $this->success(
                data: [
                    'room_rate' => $room_rate,
                    'extra_guest_charge' => $extra_guest_charge,
                    'platform_fee' => $fees->platform_fee,
                    'convenience_fee' => $fees->convenience_fee,
                    'total_charge' => $total_charge,
                    'discounted_total_charge' => $discounted_total_charge,
                    'coupon_message' => $couponMessage,
                ]
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getHotelGalleryImage($filename)
    {
        $image = asset('storage/hotels/gallery/' . $filename);
        //Sending image
        return $this->success(
            data: $image
        );
    }

    public function getHotelGalleryImages($hotel_id)
    {

        $galleryImages = '';

        $hotel_gallery = HotelGallery::where('hotel_id', $hotel_id)->get();

        foreach ($hotel_gallery as $key => $image) {
            $galleryImages .= asset('storage/hotels/gallery/' . $image->image) . ',';
        }

        return $this->success(
            data: $galleryImages
        );
    }

    public function getHotelBannerImage($filename)
    {
        $image = asset('storage/hotels/banner_image/' . $filename);
        //Sending image
        return $this->success(
            data: $image
        );
    }

    public function getHotel($slug): JsonResponse
    {
        try {
            $bookingType = json_decode(request()->query('bookingType')) ?? 1;
            $checkIn = (request()->query('checkIn') != '') ?
                Carbon::parse(request()->query('checkIn')) : Carbon::now();
            $checkOut = request()->query('checkOut') ?
                Carbon::parse(request()->query('checkOut')) : Carbon::parse($checkIn)->addDays(1);
            $stayTime = request()->query('stayTime') ?? 3;

            $hotel = Hotel::where('slug', $slug)
                ->with('getHotelAvailability')
                ->with('amenities')
                ->with('wishlist')
                ->with('hotel_galleries')
                ->with('getBookingType')
                ->where('isActive', 1)
                ->where('isVerified', 1)
                ->where('isBanned', 0)
                ->first();

            $hotel->banner_image = asset('storage/hotels/banner_image/' . $hotel->banner_image);

            $hotel->additional_policies = Policies::all();

            $isBookingTypeAvailable = false;
            foreach ($hotel->getBookingType as $availablebookingType) {
                if ($availablebookingType->id == intval($bookingType)) {
                    $isBookingTypeAvailable = true;
                }
            }
            if ($bookingType != null && !$isBookingTypeAvailable) {
                return $this->success(
                    message: "This Booking Type service is not available for this property!",
                    data: $hotel
                );
            }

            //getting rooms according to checkIn and checkOut
            $roomController = new RoomController();

            $hotel->rooms = $roomController->getRoomsDetails(
                $hotel->id,
                $checkIn,
                $checkOut,
                $stayTime,
                $bookingType
            )->getData()->data;

            $fees = Configuration::first();

            $hotel->fees = $fees;

            return $this->success(
                data: $hotel
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getPropertyLowestRate($hotel)
    {
        $lowest_rate = 0;

        foreach ($hotel->rooms as $key => $room) {
            //getting current rate of room according to today's date
            $room_current_rate = RoomRates::where('room_id', $room->id)->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))->first();

            if ($room_current_rate) {
                $room->current_rate = $room_current_rate->rate;
            } else {
                $room->current_rate = $room->default_rate;
            }

            if ($lowest_rate == 0) {
                $lowest_rate = $room->current_rate;
            } else if ($room->current_rate < $lowest_rate) {
                $lowest_rate = $room->current_rate;
            }
        }
        return $lowest_rate;
    }

    public function getHotels(): JsonResponse
    {
        try {
            $search = request()->query('search');
            $hotels = Hotel::with('rooms')
                ->select('id', 'vendor_id', 'name', 'slug', 'description', 'property_type_id', 'banner_image', 'address', 'city_id', 'country', 'zip')
                ->where('name', 'LIKE', "%{$search}%")
                ->with('amenities')
                ->where('isActive', 1)
                ->where('isVerified', 1)
                ->where('isBanned', 0)
                //checking if vendor is active or not
                ->whereHas('getVendor', function ($query) {
                    $query->where('is_active', 1);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            //finding the lowest rate between rooms
            foreach ($hotels as $key => $hotel) {
                $lowest_rate = $this->getPropertyLowestRate($hotel);
                $hotel->lowest_rate = $lowest_rate;
                $hotel->property_type = PropertyType::where('id', $hotel->property_type_id)->first()->name;
                $hotel->banner_image = asset('storage/hotels/banner_image/' . $hotel->banner_image);
            }

            return $this->success(
                data: $hotels
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getVendorHotels(): JsonResponse
    {
        try {
            $search = request()->query('search');

            $hotels = Hotel::where('vendor_id', Auth::getVendor()->id)
                ->with('getBookingType')
                ->with('rooms')
                ->where('name', 'LIKE', "%{$search}%")
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            foreach ($hotels as $key => $hotel) {
                $hotel->property_type = PropertyType::where('id', $hotel->property_type_id)->first()->name;
                $hotel->banner_image = asset('storage/hotels/banner_image/' . $hotel->banner_image);
                $hotel->availability = PropertyAvailabilityTime::where('property_id', $hotel->id)->first();
            }

            return $this->success(
                data: $hotels
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addPropertyByAdmin(HotelRequest $request)
    {
        try {
            //adding auth vendor id to request
            $request->merge(['vendor_id' => $request->vendor_id]);
            return $this->saveProperty($request);
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addPropertyByVendor(HotelRequest $request)
    {
        try {
            //adding auth vendor id to request
            $request->merge(['vendor_id' => Auth::getVendor()->id]);
            return $this->saveProperty($request);
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    private function saveProperty(HotelRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Checking banner image, gallery, tenancy agreement, corporate documents, identity documents, address documents each image is less than 500Kb and is of the format of jpeg, png, jpg
            $imageService = new ImageUploadService();
            $requiredFiles = [
                'gallery',
                'tenancyAgreement',
                'corporateDocument',
                'identityDocuments',
                'addressDocuments'
            ];

            foreach ($requiredFiles as $file) {
                if (!$imageService->validateImages($request->file($file))) {
                    return $this->error(message: ucfirst($file) . ' Image is not valid!');
                }
            }

            $hotel = new Hotel();

            // Inserting data
            $hotel->vendor_id = $validated['vendor_id'];
            $hotel->name = $validated['name'];
            $hotel->description = $validated['description'];

            // Checking if policies and locations are in array format after decoding from string
            $isPolicyValid = json_decode($validated['policies']);
            if (!$isPolicyValid) {
                return $this->error(message: 'Policies are not valid!');
            }
            $hotel->policies = $validated['policies'];

            $isLocationValid = json_decode($validated['locations']);
            if (!$isLocationValid) {
                return $this->error(message: 'Locations are not valid!');
            }
            $hotel->nearby_locations = $validated['locations'];
            $hotel->primary_number = $validated['primary_number'];
            $hotel->secondary_number = $validated['secondary_number'];
            $hotel->primary_email = $validated['primary_email'];
            $hotel->secondary_email = $validated['secondary_email'];
            $hotel->property_type_id = $validated['propertyType'];
            $hotel->address = $validated['address'];
            $hotel->city_id = $validated['city_id'];
            $hotel->country = $validated['country'];
            $hotel->zip = $validated['zip_code'];
            $hotel->location_iframe = $validated['location_iframe'];

            // Inserting Images
            $hotel->banner_image = $imageService->uploadImage($request->file('banner_image'), '/hotels/banner_image/');
            $hotel->tenancy_agreement = $imageService->uploadImageSet($request->file('tenancyAgreement'), '/hotels/tenancy_agreement/');
            $hotel->corporate_documents = $imageService->uploadImageSet($request->file('corporateDocument'), '/hotels/corporate_documents/');
            $hotel->identity_documents = $imageService->uploadImageSet($request->file('identityDocuments'), '/hotels/identity_documents/');
            $hotel->proof_of_ownership = $imageService->uploadImageSet($request->file('addressDocuments'), '/hotels/proof_of_ownership/');

            // Checking if auth user is admin
            if (Auth::user()->role_id == 2) {
                $hotel->isVerified = 1;
            }

            if ($hotel->save()) {
                // Adding amenities
                foreach (json_decode($validated['amenities']) as $amenity) {
                    $propertyAmenity = new PropertyAmenities();
                    $propertyAmenity->property_id = $hotel->id;
                    $propertyAmenity->amenity_id = $amenity->id;
                    $propertyAmenity->save();
                }

                $this->addBookingType($validated['bookingType'], $hotel->id);

                // Uploading hotel gallery
                foreach ($request->file('gallery') as $image) {
                    $gallery = new HotelGallery();
                    $gallery->hotel_id = $hotel->id;
                    $gallery->image = $imageService->uploadImage($image, '/hotels/gallery/');
                    $gallery->save();
                }

                return $this->success(message: 'Hotel Added Successfully!');
            }

            // Deleting all images if the hotel is not saved
            $imageService->deleteImageSet($hotel->gallery, '/hotels/gallery/');
            $imageService->deleteImageSet($hotel->tenancy_agreement, '/hotels/tenancy_agreement/');
            $imageService->deleteImageSet($hotel->corporate_documents, '/hotels/corporate_documents/');
            $imageService->deleteImageSet($hotel->identity_documents, '/hotels/identity_documents/');
            $imageService->deleteImageSet($hotel->proof_of_ownership, '/hotels/proof_of_ownership/');

            return $this->error(message: 'Failed to add new Hotel!');
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function setCheckInOutTime(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(
                [
                    'check_in_time' => 'required|date_format:H:i',
                    'check_out_time' => 'required|date_format:H:i',
                    'hotel_id' => 'required|integer|exists:hotels,id'
                ]
            );

            //checking if the hotel is under the authorized vendor

            $hotel = Hotel::where('id', $validated['hotel_id'])->first();

            if (Auth::user()->role != 2 && $hotel->vendor_id != Auth::getVendor()->id) {
                return $this->error(
                    message: 'Unauthorized Access!'
                );
            }

            if (PropertyAvailabilityTime::where('property_id', $hotel->id)->exists()) {
                $availability = PropertyAvailabilityTime::where('property_id', $hotel->id)->first();
                $availability->start_time = $validated['check_in_time'];
                $availability->end_time = $validated['check_out_time'];

                if ($availability->save()) {
                    return $this->success(
                        message: 'Check In/Out Time Updated Successfully!'
                    );
                }
            } else {
                $availability = new PropertyAvailabilityTime();
                $availability->property_id = $hotel->id;
                $availability->start_time = $validated['check_in_time'];
                $availability->end_time = $validated['check_out_time'];

                if ($availability->save()) {
                    return $this->success(
                        message: 'Check In/Out Time Updated Successfully!'
                    );
                }
            }

            return $this->error(
                message: 'Failed to update Check In/Out Time!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getCheckInOutTime(Request $request): JsonResponse
    {
        try {

            $validated = $request->validate(
                [
                    'hotel_id' => 'required|integer|exists:hotels,id'
                ]
            );

            $availability = PropertyAvailabilityTime::where('property_id', $validated['hotel_id'])->first();
            return $this->success(
                data: $availability
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getCheckInOutTimeForHotel($hotel_id): JsonResponse
    {
        try {
            $availability = PropertyAvailabilityTime::where('hotel_id', $hotel_id)->first();
            return $this->success(
                data: $availability
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

}
