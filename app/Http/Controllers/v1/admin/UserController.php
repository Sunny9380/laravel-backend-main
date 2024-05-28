<?php

namespace App\Http\Controllers\v1\admin;

use App\Exports\AllUsersExport;
use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    use HttpResponse;

    public function index()
    {
        try {
            $search = request()->query('search');
            $type = request()->query('type');
            $users = null;

            if ($type == "all") {
                $users = User::where('name', 'LIKE', "%{$search}%")
                    ->orderBy('role', 'asc')
                    ->where('id', '!=', Auth::user()->id)
                    ->paginate(15);
            } else if ($type == "vendors") {
                $users = User::where('role', 1)
                    ->where('name', 'LIKE', "%{$search}%")
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
            } else if ($type == "admins") {
                $users = User::where('role', 2)
                    ->where('name', 'LIKE', "%{$search}%")
                    ->where('id', '!=', Auth::user()->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
            }

            foreach ($users as $user) {
                if ($user->image && $user->image != 'null') {
                    //checking image is not url for google logined
                    if (!filter_var($user->image, FILTER_VALIDATE_URL)) {
                        $user->image = asset('storage/user/image/' . $user->image);
                    }
                }
            }

            return $this->success(
                data: $users
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function userExport($id)
    {
        return Excel::download(new UserExport($id), 'user_' . $id . '.xlsx');
    }

    public function allUsersExport()
    {
        return Excel::download(new AllUsersExport(), 'all_users.xlsx');
    }

    public function deleteUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric'
            ]);
            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );
            $user = User::where('id', $request->id)->firstOrFail();
            $email = $user->email;
            if ($user->delete()) {
                return $this->success(
                    message: $email . ' Deleted Successfully!'
                );
            } else {
                return $this->error(
                    message: 'Failed to ' . $email
                );
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }


    public function promoteUserToAdmin(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|string',
            ]);

            if (Auth::user()->role == 2) {
                $user = User::where('id', $validated['user_id'])->firstOrFail();
                if ($user->role == 0) {
                    $user->role = 2;
                }

                if ($user->update()) {
                    return $this->success(
                        message: $user->email . ' Promoted to Admin!'
                    );
                } else {
                    return $this->error(
                        message: 'Failed to Promote ' . $user->email
                    );
                }
            } else {
                return $this->error(
                    message: 'You are not authorized to perform this action!'
                );
            }

        } catch (\Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->notFound(
                    message: 'User not found!'
                );
            }
            return $this->internalError($e->getMessage());
        }
    }


    public function blockUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|string',
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $user = User::where('id', $request->user_id)->firstOrFail();
            $user->is_blocked = !$user->is_blocked;

            if ($user->update()) {
                return $this->success(
                    message: $user->email . ($user->is_blocked ? ' Blocked!' : ' UnBlocked!')
                );
            } else {
                return $this->error(
                    message: 'Failed to ' . ($user->is_blocked ? 'Block ' : 'UnBlock ') . $user->email
                );
            }

        } catch (\Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->notFound(
                    message: 'User not found!'
                );
            }
            return $this->internalError($e->getMessage());
        }
    }


}
