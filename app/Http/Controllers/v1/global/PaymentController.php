<?php

namespace App\Http\Controllers\v1\global;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\hotel\HotelController;
use App\Http\Traits\HttpResponse;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\Configuration;
use App\Models\Coupon;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomBookingTemp;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentController extends Controller
{
    use HttpResponse;

    public function createPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'userDetails' => 'required',
                'rooms' => 'required',
                'hotel' => 'required',
                'bookingDetails' => 'required',
                'checkInTime' => 'nullable',
                'coupon_code' => 'nullable',
                'guest_name' => 'nullable'
            ]);

            // auth user can do max 6 bookings in a day
            $userBookings = Booking::where('user_id', Auth::user()->id)
                ->where('created_at', '>=', Carbon::now()->startOfDay())
                ->where('created_at', '<=', Carbon::now()->endOfDay())
                ->get();
            if (count($userBookings) >= 6) {
                return $this->error(
                    message: 'You can only do 3 bookings in 1 day.',
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
            $booking->notes = $userDetails->notes; // Payment status pending
            $booking->check_out = $bookingDetails->check_out_date ? Carbon::createFromDate($bookingDetails->check_out_date) : Carbon::now()->addDay();

            //splitting checin time into hours and minutes
            if ($request->checkInTime) {
                $booking->check_in_time = Carbon::createFromFormat('H:i:s', json_decode($validated['checkInTime']));
            }


            $paymentCapture = 1;
            $amount = $stayPrice->discounted_total_charge * 100; // Amount in paisa
            $currency = "INR";

            //calculating vendor and admin commission
            $hotel = Hotel::where('id', json_decode($validated['hotel'])->id)->with('getVendor')->first();
            $vendor = Vendor::where('id', $hotel->getVendor->id)->first();

            //calculating admin comission (vendor comission is in percent)
            $comission_fee = ($stayPrice->room_rate * $vendor->commission) / 100;
            //fetching configuration
            $configuration = Configuration::first();
            $platform_fee = $configuration->platform_fee ?? 0;
            $convenience_fee = $configuration->convenience_fee ?? 0;

            $admin_fee = $comission_fee + $platform_fee + $convenience_fee;

            $options = [
                'amount' => (string) $amount,
                'currency' => $currency,
                'receipt' => Str::uuid(),
                'payment_capture' => $paymentCapture,
                'notes' => [
                    'paymentFor' => json_decode($request->userDetails)->name,
                    'userId' => Auth::user()->id,
                    'hotelId' => json_decode($request->hotel)->id,
                ],
            ];

            //creating order
            $api = new Api(env('RAZORPAY_API_KEY'), env('RAZORPAY_APT_SECRET'));

            $order = $api->order->create($options);

            $booking->order_id = $order->id;
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
                data: [
                    'id' => $order['id'],
                    'amount' => $order->amount,
                    'currency' => $order->currency,
                    'status' => $order->status,
                    'admin_fee' => $admin_fee
                ],
                message: 'success'
            );
        } catch (\Exception $exception) {
            return $this->success(message: $exception->getMessage());
        }
    }

    public function verifyPayment(Request $request)
    {
        $validated = $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);
        $api = new Api(env('RAZORPAY_API_KEY'), env('RAZORPAY_APT_SECRET'));

        $attributes = [
            'razorpay_order_id' => $validated['razorpay_order_id'],
            'razorpay_payment_id' => $validated['razorpay_payment_id'],
            'razorpay_signature' => $validated['razorpay_signature'],
        ];

        try {
            $api->utility->verifyPaymentSignature($attributes);

            $booking = Booking::where('order_id', $validated['razorpay_order_id'])->first();
            $booking->razorpay_payment_id = $validated['razorpay_payment_id'];
            $booking->razorpay_signature = $validated['razorpay_signature'];
            $booking->payment_status = 'Paid';

            return $this->success(
                data: $booking->update(),
                message: 'success'
            );

        } catch (SignatureVerificationError $e) {
            // Signature verification failed
            return response()->json([
                'message' => 'fail',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function transferMoney(Request $request)
    {
        try {
            // Get the transfer data from the request
            $validated = $request->validate([
                'order_id' => 'required',
                'account' => 'required',
                'amount' => 'required',
                'currency' => 'required',
                'notes' => 'required',
            ]);

            $booking = Booking::where('order_id', $validated['order_id'])->first();
            if ($booking->transfer_status == 'transferred') {
                return $this->error([
                    'message' => 'Already transferred',
                ]);
            }

            // Initialize the Razorpay API client
            $api = new Api(env('RAZORPAY_API_KEY'), env('RAZORPAY_APT_SECRET'));

            // Create the transfer
            $transfer = $api->transfer->create([
                'account' => $validated['account'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'notes' => $validated['notes'],
            ]);

            if ($transfer->status == 'transferred') {
                $booking->transfer_status = 'transferred';
                $booking->update();

                return $this->success(
                    data: $transfer,
                    message: 'success'
                );
            }
            $booking->transfer_status = 'failed';
            $booking->update();
            return $this->error([
                'message' => 'Transfer failed',
            ]);
        } catch (SignatureVerificationError $e) {
            // Signature verification failed
            return response()->json([
                'message' => 'fail',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

}
