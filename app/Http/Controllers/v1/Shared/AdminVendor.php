<?php

namespace App\Http\Controllers\v1\Shared;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\City;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminVendor extends Controller
{
    use HttpResponse;

    public function __construct()
    {
        $this->middleware(['middleware' => 'VendorAccessGuard']);
    }

    public function getVendor(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric'
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $vendor = Vendor::find($request->id);

            if (!$vendor)
                return $this->notFound(
                    message: 'Vendor not found!'
                );

            return $this->success(
                data: $vendor
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
