<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\PropertyType;
use App\Models\User;
use App\Services\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SharedAuthRoutes extends Controller
{
    use HttpResponse;

    public function getPropertyType($id)
    {
        try {
            $type = PropertyType::find($id);
            return $this->success(
                data: $type
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'newPassword' => 'required|string',
                'currentPassword' => 'required|string'
            ]);

            if (!Hash::check($validated['currentPassword'], Auth::user()->password)) {
                return $this->error(
                    message: 'Invalid Current Password!'
                );
            }

            $user = Auth::user();

            $user->password = bcrypt($validated['newPassword']);
            if ($user->update()) {
                return $this->success(
                    message: 'Password Changed Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to change password!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getPropertyTypes()
    {
        try {
            $search = request()->query('search');

            //adding with relation to get properties count
            $types = PropertyType::where('name', 'LIKE', "%{$search}%")
                ->withCount('properties')
                ->paginate(15);

            return $this->success(
                data: $types
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function details()
    {
        $user = Auth::user();
        if ($user->image) {
            //checking image is not url for google logined
            if (!filter_var($user->image, FILTER_VALIDATE_URL)) {
                $user->image = asset('storage/user/image/' . $user->image);
            }
        }
        return $this->success(
            data: $user
        );
    }

    public function updateDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'address' => 'required|string',
            'phoneNumber' => 'required|string',
            'gender' => 'required|string',
            'date' => 'required',
            'image' => 'nullable'
        ]);

        if ($validator->fails())
            return $this->error(
                message: 'Invalid Request!'
            );
        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->address = $request->address;
        $user->phone_number = $request->phoneNumber;
        $user->gender = $request->gender;
        $user->dob = Carbon::createFromDate($request->date)->format('Y-m-d');

        if ($request->hasFile('image')) {
            $imageService = new ImageUploadService();
            $image = $imageService->updateImage($user->image, $request->file('image'), '/user/image/');
            $user->image = $image;
        }

        if ($user->update()) {
            return $this->success(
                message: 'Details Updated Successfully!'
            );
        } else {
            return $this->error(
                message: 'Failed to update details!'
            );
        }
    }
}
