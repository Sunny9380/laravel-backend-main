<?php

namespace App\Http\Controllers\v1\hotel;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetAllPropertiesBookingsRequest;
use App\Http\Traits\HttpResponse;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    use HttpResponse;

    public function getAllPropertiesBookings(GetAllPropertiesBookingsRequest $request)
    {
        try {
            $bookingType = request()->query('bookingType');
            $search = request()->query('search');
            $hotels = Vendor::with('hotels')
                ->first()
                ->hotels;
            $bookings = [];

            foreach ($hotels as $hotel) {
                $query = Booking::where('hotel_id', $hotel->id)
                    ->orderBy('created_at', 'desc')
                    ->where('is_cancelled', 0);

                if ($bookingType === 'upcoming') {
                    $query->where('payment_status', 'paid')->where('check_in', '>', date('Y-m-d'));
                } elseif ($bookingType === 'ongoing') {
                    $query->where('payment_status', 'paid')->where('check_in', '<=', date('Y-m-d'))->where('check_out', '>=', date('Y-m-d'));
                } elseif ($bookingType === 'past') {
                    $query->where('payment_status', 'paid')->where('check_out', '<', date('Y-m-d'));
                } elseif ($bookingType === 'cancelled') {
                    $query->where('is_cancelled', 1);
                } else {
                    $query->where('payment_status', 'paid');
                }

                //searching by booking_id
                if ($search) {
                    $query->where('booking_id', 'LIKE', "%{$search}%");
                }

                $booking_by_hotel = $query->get()->toArray();
                //adding hotel name in each booking
                foreach ($booking_by_hotel as $key => $booking) {
                    $booking_by_hotel[$key]['hotel_name'] = $hotel->name;
                    $booking_by_hotel[$key]['image'] = $hotel->banner_image;
                    $user = User::where('id', $booking_by_hotel[$key]['user_id'])->first();
                    $booking_by_hotel[$key]['user'] = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone_number,
                    ];
                }

                $bookings = array_merge($bookings, $booking_by_hotel);
            }

            $perPage = 15; // You can adjust the number of items per page as needed
            $currentPage = Paginator::resolveCurrentPage('page');

            $currentItems = array_slice($bookings, ($currentPage - 1) * $perPage, $perPage);
            $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, count($bookings), $perPage);

            return $this->success(data: $paginatedData);
        } catch (\Exception $e) {
            return $this->error([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getBookings()
    {
        try {
            $bookingType = request()->query('bookingType');
            $search = request()->query('search');
            $hotels = Vendor::where('user_id', Auth::user()->id)->with('hotels')->first()->hotels;
            $bookings = [];

            foreach ($hotels as $hotel) {
                $query = Booking::where('hotel_id', $hotel->id)
                    ->orderBy('created_at', 'desc')
                    ->where('is_cancelled', 0);

                if ($bookingType === 'upcoming') {
                    $query->where('payment_status', 'paid')->where('check_in', '>', date('Y-m-d'));
                } elseif ($bookingType === 'ongoing') {
                    $query->where('payment_status', 'paid')->where('check_in', '<=', date('Y-m-d'))->where('check_out', '>=', date('Y-m-d'));
                } elseif ($bookingType === 'past') {
                    $query->where('payment_status', 'paid')->where('check_out', '<', date('Y-m-d'));
                } elseif ($bookingType === 'cancelled') {
                    $query->where('is_cancelled', 1);
                } else {
                    $query->where('payment_status', 'paid');
                }

                //searching by booking_id
                if ($search) {
                    $query->where('booking_id', 'LIKE', "%{$search}%");
                }

                $booking_by_hotel = $query->get()->toArray();
                //adding hotel name in each booking
                foreach ($booking_by_hotel as $key => $booking) {
                    $booking_by_hotel[$key]['hotel_name'] = $hotel->name;
                    $booking_by_hotel[$key]['image'] = $hotel->banner_image;
                    $user = User::where('id', $booking_by_hotel[$key]['user_id'])->first();
                    $booking_by_hotel[$key]['user'] = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone_number,
                    ];
                }

                $bookings = array_merge($bookings, $booking_by_hotel);
            }

            $perPage = 15; // You can adjust the number of items per page as needed
            $currentPage = Paginator::resolveCurrentPage('page');

            $currentItems = array_slice($bookings, ($currentPage - 1) * $perPage, $perPage);
            $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, count($bookings), $perPage);

            return $this->success(data: $paginatedData);
        } catch (\Exception $e) {
            return $this->error([
                'message' => $e->getMessage()
            ]);
        }
    }
}
