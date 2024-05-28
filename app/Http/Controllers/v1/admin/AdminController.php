<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\Booking;
use App\Models\City;
use App\Models\Policies;
use App\Models\PolicyItem;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use HttpResponse;

    public function addPolicyHeading(Request $request)
    {
        try {
            $validated = $request->validate([
                'policyHeading' => 'required|string'
            ]);

            $policy = new Policies();
            $policy->name = $validated['policyHeading'];
            if ($policy->save()) {
                return $this->success(
                    message: 'Policy added successfully'
                );
            }
            return $this->error(
                message: 'Failed to add policy'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function deletePolicyHeading(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer'
            ]);

            $policy = Policies::find($validated['id']);
            if ($policy->delete()) {
                return $this->success(
                    message: 'Policy deleted successfully'
                );
            }
            return $this->error(
                message: 'Failed to delete policy'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function addPolicyItem(Request $request)
    {
        try {
            $validated = $request->validate([
                'policyId' => 'required|integer',
                'policyItem' => 'required|string'
            ]);

            $policyItem = new PolicyItem();
            $policyItem->policy_id = $validated['policyId'];
            $policyItem->policy = $validated['policyItem'];
            if ($policyItem->save()) {
                return $this->success(
                    message: 'Policy item added successfully'
                );
            }
            return $this->error(
                message: 'Failed to add policy item'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function deletePolicyItem(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer'
            ]);

            $policyItem = PolicyItem::find($validated['id']);
            if ($policyItem->delete()) {
                return $this->success(
                    message: 'Policy item deleted successfully'
                );
            }
            return $this->error(
                message: 'Failed to delete policy item'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function togglePolicyStatus($id)
    {
        try {
            $policy = Policies::find($id);
            $policy->is_active = !$policy->is_active;
            if ($policy->save()) {
                return $this->success(
                    message: 'Policy status updated successfully'
                );
            }
            return $this->error(
                message: 'Failed to update policy status'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getPolicies()
    {
        $policies = Policies::with('policyItems')->get();

        return $this->success($policies);
    }

    public function getAllStatics()
    {
        //fetching number of vendors, total cities, registered users, new users, total booking, total cancel, total earning deatils from database with per month growth
        try {
            $vendors = Vendor::count();
            $cities = City::count();
            $users = User::count();

            $vendorGrowth = Vendor::select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('count(id) as total'))->groupBy('month', 'year')->get();

            $citiesGrowth = City::select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('count(id) as total'))->groupBy('month', 'year')->get();

            $usersGrowth = User::select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('count(id) as total'))->groupBy('month', 'year')->get();

            $newUsers = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
            $totalBooking = Booking::count();
            $totalCancel = Booking::where('is_cancelled', 1)->count();
            $totalEarning = Booking::where('payment_status', '!=', 'pending')
                ->where('is_cancelled', 0)
                ->sum('amount');

            $monthlyGrowth = Booking::select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('count(id) as total'))->groupBy('month', 'year')->get();

            return $this->success([
                'vendors' => $vendors,
                'cities' => $cities,
                'users' => $users,
                'vendorGrowth' => $vendorGrowth,
                'citiesGrowth' => $citiesGrowth,
                'usersGrowth' => $usersGrowth,
                'newUsers' => $newUsers,
                'totalBooking' => $totalBooking,
                'totalCancel' => $totalCancel,
                'totalEarning' => $totalEarning,
                'monthlyGrowth' => $monthlyGrowth
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


}
