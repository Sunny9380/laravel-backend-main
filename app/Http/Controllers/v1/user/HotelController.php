<?php

namespace App\Http\Controllers\v1\user;

use App\Exports\ExportVendorBankDetails;
use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Jobs\ProcessWelcomeVendorEmail;
use App\Jobs\vendor\WelcomeVendorMail;
use App\Mail\SendCodeResetPassword;
use App\Mail\VerifyBusinessMail;
use App\Models\PropertyRequest;
use App\Models\RequestVerifiedMail;
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\vendor\WelcomeVendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Snowfire\Beautymail\Beautymail;

class HotelController extends Controller
{
    use HttpResponse;

    public function test()
    {
       $userId = 3;
        return Excel::download(new ExportVendorBankDetails($userId), 'user_data_'.$userId.'.xlsx');
    }

    public function isBusinessEmailVerified($email)
    {
        try {
            if (RequestVerifiedMail::where('email', $email)->exists()) {
                $isVerifiedMail = RequestVerifiedMail::where('email', $email)->first();
                //if already verified
                if ($isVerifiedMail->verified_at != null) {
                    return $this->success(
                        message: 'Email verified'
                    );
                }

                return $this->error(
                    message: 'Email not verified'
                );
            } else {
                return $this->error(
                    message: 'Email not verified'
                );
            }
        } catch (\Throwable $e) {
            return $this->error(
                data: false,
                message: $e->getMessage()
            );
        }
    }

    public function verifyBusinessEmailToken($token)
    {
        try {
            //validating token
            if (!RequestVerifiedMail::where('verify_token', $token)->exists()) {
                return $this->error(
                    message: 'Invalid token'
                );
            }

            $verifiedMail = RequestVerifiedMail::where('verify_token', $token)->first();

            //if already verified
            if ($verifiedMail->verified_at != null) {
                return $this->success(
                    message: 'Email already verified! You can now submit your property'
                );
            }

            $verifiedMail->verified_at = now();

            if ($verifiedMail->save()) {
                return $this->success(
                    message: 'Email Verified Successfully! You can now submit your property'
                );
            } else {
                return $this->error(
                    message: 'Error verifying email'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                data: false,
                message: $e->getMessage()
            );
        }
    }

    public function verifyPropertyMail($email)
    {
        try {
            //checking is email is valid
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->error(
                    message: 'Invalid email'
                );
            }

            $token = md5($email . time());

            if (RequestVerifiedMail::where('email', $email)->exists()) {
                $isVerifiedMail = RequestVerifiedMail::where('email', $email)->first();

                //if already verified
                if ($isVerifiedMail->verified_at != null) {
                    return $this->error(
                        message: 'Email already verified'
                    );
                }

                //resetting the times sent if mail sent after 24 hrs
                if ($isVerifiedMail->mail_sent_at != null) {
                    $mailSentAt = Carbon::parse($isVerifiedMail->mail_sent_at); // Parse using Carbon
                    $currentTime = Carbon::now();

                    $hoursDiff = $currentTime->diffInHours($mailSentAt); // Calculate difference directly in hours

                    if ($hoursDiff >= 24) { // Check for greater than or equal to 24 hours
                        $isVerifiedMail->times_sent = 0;
                        $isVerifiedMail->mail_sent_at = null;
                        $isVerifiedMail->save();
                    }

                    // If mail sent more than 5 times within 24 hours
                    if ($isVerifiedMail->times_sent > 5) {
                        return $this->error(
                            message: 'Mail sent more than 5 times within 24 hours'
                        );
                    }
                }

                $isVerifiedMail->times_sent = $isVerifiedMail->times_sent + 1;
                $isVerifiedMail->mail_sent_at = now();
                $isVerifiedMail->verify_token = $token;
                $isVerifiedMail->save();
            } else {
                //One user can submit only one email
                if (RequestVerifiedMail::where('user_id', Auth::user()->id)->exists()) {
                    return $this->error(
                        message: 'One user can submit only one email! If you want to change email, please contact support'
                    );
                }
                $addVerifyRequest = new RequestVerifiedMail();
                $addVerifyRequest->user_id = Auth::user()->id;
                $addVerifyRequest->email = $email;
                $addVerifyRequest->times_sent = 1;
                $addVerifyRequest->mail_sent_at = now();
                $addVerifyRequest->verify_token = md5($email . time());
                $addVerifyRequest->save();
            }
            if ($email) {
                $beautymail = app()->make(Beautymail::class);
                $beautymail->send(
                    'emails.users.verify-business-mail',

                    ['url' => env('FRONTEND_URL') . "/list-property/mail-verify?token=" . $token],
                    function ($message) use ($email) {
                        $message
                            ->from(env('MAIL_FROM_ADDRESS', 'staymytrip@gmail.com'))
                            ->to($email)
                            ->subject('Business Email Verification');
                    }
                );
            }


            return $this->success(
                message: 'Verification mail sent successfully! Please check your email'
            );

        } catch (\Throwable $e) {
            return $this->error(
                data: false,
                message: $e->getMessage()
            );
        }
    }

    public function requestProperty(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'name' => 'required|string',
                    'email' => 'required|email',
                    'phone' => 'required|string',
                    'type' => 'required|string',
                    'gst_number' => 'required|string',
                    'address' => 'required|string',
                    'password' => 'required|string',

                ]
            );

            $user = Auth::user();
            //if email or user id exsts
            if (PropertyRequest::where('email', $validated['email'])->orWhere('user_id', $user->id)->exists()) {
                return $this->error(
                    message: 'Request already submitted!',
                );
            }

            if (!$user) {
                return $this->error(
                    message: 'User not logged in',
                );
            }

            if ($user->role == 1 || $user->role == 2) {
                return $this->error(
                    message: 'Vendor cannot request for property',
                );
            }

            $propetyRequest = new PropertyRequest();
            $propetyRequest->user_id = $user->id;
            $propetyRequest->name = $validated['name'];
            $propetyRequest->email = $validated['email'];
            $propetyRequest->phone = $validated['phone'];
            $propetyRequest->type = $validated['type'];
            $propetyRequest->gst_number = $validated['gst_number'];
            $propetyRequest->address = $validated['address'];
            $propetyRequest->password = $validated['password'];

            if ($propetyRequest->save()) {
                return $this->success(
                    message: 'Request submitted successfully'
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                data: false
            );
        }
    }

    public function getWishlist()
    {
        try {
            if (!Auth::check()) {
                return $this->error(
                    message: 'User not logged in'
                );
            }

            $search = request()->query('search');

            $userId = Auth::user()->id;

            $wishlist = Wishlist::where('user_id', $userId)->get();

            $hotels = [];
            foreach ($wishlist as $value) {
                $hotels[] = $value->hotel;
            }

            return $this->success(
                data: $hotels
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getHotelWishlistStatus(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'hotel_id' => 'required|integer|exists:hotels,id',
                ]
            );
            if (!Auth::check()) {
                return $this->error(
                    message: 'User not logged in'
                );
            }

            $userId = Auth::user()->id;

            $wishlist = Wishlist::where('user_id', $userId)->where('hotel_id', $validated['hotel_id'])->exists();

            return $this->success(
                data: $wishlist
            );
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addRemoveHotelToWishList(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'hotel_id' => 'required|integer|exists:hotels,id',
                ]
            );

            if (!Auth::check()) {
                return $this->error(
                    data: false
                );
            }

            $userId = Auth::user()->id;

            if (Wishlist::where('hotel_id', $validated['hotel_id'])->where('user_id', $userId)->exists()) {
                Wishlist::where('hotel_id', $validated['hotel_id'])->where('user_id', $userId)->delete();

                return $this->success(
                    data: true
                );
            } else {

                $wishlist = new Wishlist();
                $wishlist->hotel_id = $validated['hotel_id'];
                $wishlist->user_id = $userId;
                $wishlist->save();

                return $this->success(
                    data: true
                );
            }

        } catch (\Throwable $e) {
            return $this->error(
                data: false
            );
        }
    }
}
