<?php

namespace App\Http\Controllers\v1\Shared;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\Complain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplainController extends Controller
{
    use HttpResponse;

    public function getAllComplains()
    {
        try {
            $search = request()->query('search');

            $complains = Complain::with('property', 'user')
                ->whereHas('property', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })
                ->orderBy('created_at', 'desc')
                ->orderBy('status', 'asc')
                ->paginate(10);

            foreach ($complains as $complain) {
                $complain->vendor = $complain->property->vendor;
            }

            return $this->success(
                data: $complains
            );

        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addUserComplain(Request $request)
    {
        try {
            $valdiated = $request->validate([
                'property_id' => 'required|exists:hotels,id',
                'title' => 'required|string',
                'description' => 'required|string',
            ]);

            // max pending complain user can in 1 property is 2
            $complainCount = Complain::where('user_id', Auth::user()->id)
                ->where('property_id', $valdiated['property_id'])
                ->where('status', 'pending')
                ->count();
            if ($complainCount >= 2) {
                return $this->error(
                    message: 'You can only have 2 pending complains in a property'
                );
            }

            $complain = new Complain();
            $complain->user_id = Auth::user()->id;
            $complain->property_id = $valdiated['property_id'];
            $complain->title = $valdiated['title'];
            $complain->description = $valdiated['description'];

            if ($complain->save()) {
                return $this->success(
                    message: 'Complain added successfully'
                );
            }

            return $this->error(
                message: 'Failed to add complain'
            );

        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
