<?php

namespace App\Http\Controllers\v1\auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Exception;
use App\Http\Traits\HttpResponse;
use App\Jobs\admin\SendBulkMails;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponse;

    public function authenticate(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string',
                'email' => 'required|email',
                'google_id' => 'nullable',
                'password' => 'nullable',
                'image' => 'nullable|string'
            ]);

            $user = User::whereEmail($request->email)->first();
            //If email exists
            if ($user) {
                //checking if user is not blocked
                if ($user->is_blocked) {
                    return $this->notFound(
                        message: 'You are blocked by admin!'
                    );
                }

                $template = EmailTemplate::where('type', 'welcome')->first();

                $emails = [$request->email];

                 // Sending email by SendBulkEmail Job
                SendBulkMails::dispatch($emails, $template->subject, $template->body);

                return $this->login(
                    email: $request->email,
                    password: $request->password ?? '',
                    google_id: $request->google_id ?? ''
                );
            }
            //IF email not exists
            if ($request->google_id && !$request->name) {
                return $this->notFound(
                    message: 'Invalid Credentials1!'
                );
            }


            return $this->register(
                name: $request->name ?? "",
                email: $request->email ?? "",
                password: $request->password ?? "",
                google_id: $request->google_id ?? "",
                image: $request->image ?? ""
            );


        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    protected function login(string $email, string $password, string $google_id): \Illuminate\Http\JsonResponse
    {
        try {
            if ($google_id !== 'null') {
                $user = User::where([
                    ['google_id', '=', $google_id],
                    ['email', '=', $email]
                ])->firstOrFail();
                Auth::login($user);
                $token = $user->createToken('accessToken')->accessToken;

                $data = [
                    'user' => $user,
                    'token' => $token
                ];

                if ($user->image) {
                    //checking image is not url for google logined
                    if (!filter_var($user->image, FILTER_VALIDATE_URL)) {
                        $user->image = asset('storage/user/image/' . $user->image);
                    }
                }

                return $this->success(
                    data: $data,
                    message: "Successfully Logged in!"
                );
            }

            //If user is logging with email and password
            $credentials = [
                'email' => $email,
                'password' => $password
            ];
            if (Auth::attempt($credentials)) {
                $token = Auth::user()->createToken('accessToken')->accessToken;
                $data = [
                    'user' => Auth::user(),
                    'token' => $token
                ];

                return $this->success(
                    data: $data,
                    message: "Successfully Logged in!"
                );
            }

            return $this->notFound(
                message: 'Invalid Credentials3!'
            );
        } catch (\Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->notFound(
                    message: 'Invalid Credentials2!'
                );
            }
            return $this->internalError($e->getMessage());
        }
    }

    protected function register(string $name, string $email, string $password, string $google_id, string $image): \Illuminate\Http\JsonResponse
    {

        try {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->google_id = $google_id;
            $user->image = $image;
            if ($google_id === 'null') {
                $user->password = bcrypt($password);
            }

            if ($user->save()) {
                $user->sendEmailVerificationNotification();
                return $this->login(
                    email: $email,
                    password: $password,
                    google_id: $google_id
                );
            } else {
                return $this->internalError('Something went wrong!');
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

}
