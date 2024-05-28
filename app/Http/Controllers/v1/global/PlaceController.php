<?php

namespace App\Http\Controllers\v1\global;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\hotel\HotelController;
use App\Http\Traits\HttpResponse;
use App\Models\City;
use App\Models\Room;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PlaceController extends Controller
{
    use HttpResponse;

    public function getAllStates()
    {
        try {
            $statesData = State::where('is_stopped', 0)->get();
            $states = [];
            foreach ($statesData as $state) {
                $states[] = [
                    'id' => $state->id,
                    'name' => $state->name,
                ];
            }
            return $this->success(
                data: $states
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getCityProperties($city)
    {
        $city = City::where('slug', $city)->first();
        $properties = $city->properties()
            ->where('isActive', 1)
            ->where('isVerified', 1)
            ->where('isBanned', 0)
            ->paginate(15);


        //finding the lowest rate between rooms
        $hotelController = new HotelController();
        foreach ($properties as $key => $property) {
            $lowest_rate = $hotelController->getPropertyLowestRate($property);
            $property->lowest_rate = $lowest_rate;
        }

        return $this->success(
            data: $properties
        );
    }

    public function getAllCities()
    {
        try {
            $search = request()->query('search');

            $cities = City::where('is_stopped', 0)
                ->where('name', 'LIKE', "%{$search}%")
                ->paginate(15);
            $data = [];
            foreach ($cities as $city) {
                $propertiesInCity = $city->properties()->count();

                //calculating average price
                $properties = $city->properties()->get();
                $totalPrice = 0;
                $hotelController = new HotelController();
                foreach ($properties as $property) {
                    $totalPrice += $hotelController->getPropertyLowestRate($property);
                }
                $averagePrice = $totalPrice / $propertiesInCity;

                $city->average_price = $averagePrice;
                $city->properties = $propertiesInCity;
                $image = asset('storage/places/cities/' . $city->image);
                $city->image = $image;
            }
            return $this->success(
                data: $cities
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getSomeCities()
    {
        try {
            $cities = City::limit(4)->where('is_stopped', 0)->get();
            $data = [];
            foreach ($cities as $city) {
                $propertiesInCity = $city->properties()->count();

                //calculating average price
                $properties = $city->properties()->get();
                $totalPrice = 0;
                $hotelController = new HotelController();
                foreach ($properties as $property) {
                    $totalPrice += $hotelController->getPropertyLowestRate($property);
                }
                $averagePrice = $totalPrice / $propertiesInCity;

                $data[] = [
                    'name' => $city->name,
                    'slug' => $city->slug,
                    'image' => asset('storage/places/cities/' . $city->image),
                    'properties' => $propertiesInCity,
                    'average_price' => $averagePrice
                ];
            }
            return $this->success(
                data: $data
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getState($id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|numeric'
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $state = State::find($id);

            if (!$state)
                return $this->notFound(
                    message: 'State not found!'
                );

            return $this->success(
                data: $state
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getCity($id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|numeric'
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $city = City::find($id);

            if (!$city)
                return $this->notFound(
                    message: 'City not found!'
                );

            return $this->success(
                data: $city
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getStates(): JsonResponse
    {
        try {
            $search = request()->query('search');
            $states = State::where('name', 'LIKE', "%{$search}%")->withCount('cities')->paginate(15);
            foreach ($states as $state) {
                $image = asset('storage/places/states/' . $state->image);
                $state->image = $image;
            }
            return $this->success(
                data: $states
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getStateImage($filename)
    {
        $image = asset('storage/places/states/' . $filename);
        //Sending image
        return $this->success(
            data: $image
        );
    }

    public function getCities(): JsonResponse
    {
        try {
            $search = request()->query('search');
            $cities = City::where('name', 'LIKE', "%{$search}%")->with('hotels')->with('bookings')->paginate(15);
            foreach ($cities as $city) {
                $image = asset('storage/places/cities/' . $city->image);
                $city->image = $image;
            }
            return $this->success(
                data: $cities
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getCityByState($state_id): JsonResponse
    {
        try {
            $validator = Validator::make(['state_id' => $state_id], [
                'state_id' => 'required|numeric'
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $cities = City::where('state_id', $state_id)->get();
            return $this->success(
                data: $cities
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getCityImage($filename)
    {
        $image = asset('storage/places/cities/' . $filename);
        //Sending image
        return $this->success(
            data: $image
        );
    }
}
