<?php

namespace App\Http\Controllers\v1\auth;

use App\Models\ResetCodePassword;
use Illuminate\Foundation\Auth\User;
use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use Illuminate\Http\Request;


class ResetPasswordController extends Controller
{
    use HttpResponse;


    public function __invoke(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
                'code' => 'required|string|exists:reset_code_passwords',
                'password' => 'required|string|min:6',
            ]);

            $passwordReset = ResetCodePassword::where('code', $validated['code'])->where('email', $validated['email'])->first();

            //checking code is expire or not
            if ($passwordReset->updated_at->addMinutes(60) < now()) {
                return $this->error(
                    message: "Code is Expire",
                    code: 422
                );
            }

            $user = User::firstWhere('email', $validated['email']);

            //updating password
            $user->password = bcrypt($validated['password']);
            $user->update();

            //deleting the code
            $passwordReset->delete();

            return $this->success(
                message: "Password Reset Successfully",
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage(),
            );
        }
    }
}
