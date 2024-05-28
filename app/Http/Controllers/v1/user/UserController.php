<?php

namespace App\Http\Controllers\v1\user;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Exception;
use App\Http\Traits\HttpResponse;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class UserController extends Controller
{
    use HttpResponse;

    public function isUserExists(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'email' => 'required|email',
                ]
            );

            $user = User::where('email', $validated['email'])->first();
            if (is_null($user)) {
                return $this->notFound(null, 'User Not Found');
            }
            return $this->success($user, 'User Retrieved Successfully');
        } catch (\Throwable $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }
}
