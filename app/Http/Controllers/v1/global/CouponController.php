<?php

namespace App\Http\Controllers\v1\global;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\Booking;
use App\Models\City;
use App\Models\Coupon;
use App\Models\CouponElegibility;
use App\Models\CouponValidProperties;
use App\Models\CouponValidStates;
use App\Models\Hotel;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    use HttpResponse;

    public function getCouponDiscountedPrice(Request $request)
    {
        try {
            $validated = $request->validate([
                'coupon_code' => 'required',
                'price' => 'required|integer',
                'property_id' => 'required|integer|exists:hotels,id'
            ]);

            $now = Carbon::now();

            $coupon = Coupon::where('code', trim($validated['coupon_code']))
                ->with('couponElegibility')
                ->where('valid_from', '<=', $now)
                ->first();

            if (!$coupon) {
                return $this->notFound(
                    message: 'Coupon Not Found!'
                );
            }

            if ($coupon->no_end_date == 0 && $coupon->valid_till < $now) {
                return $this->notFound(
                    message: 'Coupon Expired!'
                );
            }

            //checking number of coupon available
            if ($coupon->num_of_coupons != 0) {
                $used_coupons = Booking::where('coupon_id', $coupon->id)->count();
                if ($used_coupons > $coupon->num_of_coupons) {
                    return $this->notFound(
                        message: 'Coupon Expired!'
                    );
                }
            }

            //Limit of coupon that a user can use
            if ($coupon->limit_per_use) {
                $used_coupons = Booking::where('coupon_id', $coupon->id)
                    ->where('user_id', Auth::user()->id)
                    ->count();
                if ($used_coupons >= $coupon->limit_per_use) {
                    return $this->notFound(
                        message: 'Coupon Expired!'
                    );
                }
            }

            $property = Hotel::where('id', $validated['property_id'])->first();
            if (!$property) {
                return $this->notFound(
                    message: 'Hotel Not Found!'
                );
            }

            //CHECKING COUPON VALIDIDTY
            if ($coupon->source_type == 'vendor') {
                $valid_properties = CouponValidProperties::where('coupon_id', $coupon->id)->get();

                //checking coupon validity
                if (!$valid_properties->contains('property_id', $property->id)) {
                    return $this->notFound(
                        message: 'Coupon is not valid for this property!'
                    );
                }
            } else if ($coupon->source_type == 'admin') {
                $valid_states = CouponValidStates::where('coupon_id', $coupon->id)->get();

                $hotel_state_id = City::where('id', $property->city_id)->first()->state_id;

                //checking coupon validity
                if (!$valid_states->contains('state_id', $hotel_state_id)) {
                    return $this->notFound(
                        message: 'Coupon is not valid for this property!'
                    );
                }
            }

            //applying discount
            if ($coupon->is_discount_in_percent == 1) {
                $coupon->discounted_price = $validated['price'] - ($validated['price'] * ($coupon->discount / 100));
            } else {
                $coupon->discounted_price = $validated['price'] - $coupon->discount;
            }

            //validating on price range
            if ($coupon->couponElegibility->is_price_valid) {
                if ($coupon->couponElegibility->price_range_from && $coupon->couponElegibility->price_range_to) {
                    if ($coupon->discounted_price < $coupon->couponElegibility->price_range_from || $coupon->discounted_price > $coupon->couponElegibility->price_range_to) {
                        return $this->notFound(
                            message: 'Coupon is not valid for this price range!'
                        );
                    }
                }
            }

            //validating on new user
            if ($coupon->couponElegibility->is_new_user_eligible) {
                //for user who has registerd within 2 month
                $user = Auth::user();
                $user_created_at = Carbon::parse($user->created_at);
                $now = Carbon::now();
                $diff = $user_created_at->diffInMonths($now);
                if ($diff > 2) {
                    return $this->notFound(
                        message: 'Coupon is not valid for you!'
                    );
                }
            }

            return $this->success(
                message: 'Discount Applied Successfully!',
                data: $coupon->discounted_price
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getCoupons()
    {
        try {
            $search = request()->query('search');

            $coupons = Coupon::with('couponElegibility', 'couponValidStates')
                ->where('name', 'LIKE', "%{$search}%")
                ->paginate(15);

            return $this->success(
                message: 'Coupons Fetched Successfully!',
                data: $coupons
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }
    public function getVendorCoupons()
    {
        try {
            $search = request()->query('search');

            $vendor = Vendor::where('user_id', auth()->user()->id)->first();
            if (!$vendor) {
                return $this->notFound(
                    message: 'Vendor Not Found!'
                );
            }

            $coupons = Coupon::where('source_type', 'vendor')
                ->where('source_id', auth()->user()->id)
                ->where('name', 'LIKE', "%{$search}%")
                ->with('couponElegibility', 'couponValidProperties')
                ->paginate(15);

            return $this->success(
                message: 'Coupons Fetched Successfully!',
                data: $coupons
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function toggleCouponStatus(Request $request)
    {
        try {

            $validated = $request->validate([
                'coupon_id' => 'required|integer|exists:coupons,id'
            ]);

            $coupon = Coupon::where('id', $validated['coupon_id'])->first();

            if (!$coupon) {
                return $this->notFound(
                    message: 'Coupon Not Found!'
                );
            }

            $coupon->is_active = !$coupon->is_active;
            $coupon->update();

            return $this->success(
                message: 'Coupon Status Successfully changed!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function togglePropertyCouponStatus(Request $request)
    {
        try {

            $validated = $request->validate([
                'coupon_id' => 'required|integer|exists:coupons,id'
            ]);

            $coupon = Coupon::where('id', $validated['coupon_id'])->first();

            if (!$coupon) {
                return $this->notFound(
                    message: 'Coupon Not Found!'
                );
            }

            $coupon->is_active = !$coupon->is_active;
            $coupon->update();

            return $this->success(
                message: 'Coupon Status Successfully changed!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deleteAdminCoupon(Request $request)
    {
        try {

            $validated = $request->validate([
                'coupon_id' => 'required|integer|exists:coupons,id'
            ]);

            $coupon = Coupon::where('id', $validated['coupon_id'])->first();

            if (!$coupon) {
                return $this->notFound(
                    message: 'Coupon Not Found!'
                );
            }

            $coupon->delete();

            return $this->success(
                message: 'Coupon Deleted Successfully!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deletePropertyCoupon(Request $request)
    {
        try {

            $validated = $request->validate([
                'coupon_id' => 'required|integer|exists:coupons,id'
            ]);

            $coupon = Coupon::where('id', $validated['coupon_id'])->first();

            if (!$coupon) {
                return $this->notFound(
                    message: 'Coupon Not Found!'
                );
            }

            $coupon->delete();

            return $this->success(
                message: 'Coupon Deleted Successfully!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addAdminCoupon(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
                'description' => 'required|string',
                'limit_per_use' => 'required|integer',
                'is_discount_in_percent' => 'required',
                'discount' => 'required|integer',
                'elegibility' => 'required|string',
                'starting_price' => 'nullable|integer',
                'ending_price' => 'nullable|integer',
                'valid_from' => 'required|date',
                'valid_till' => 'required|date',
                'noEndDate' => 'nullable',
                'elegible_states' => 'required',
                'is_num_of_coupons' => 'nullable',
                'num_of_coupons' => 'nullable|integer',
                'conditions' => 'required|string',
            ]);

            //checking if the coupon is_discount_in_percent is 1 then the discount must be between 0-100
            if ($validated['is_discount_in_percent'] == 1) {
                if ($validated['discount'] < 0 || $validated['discount'] > 100) {
                    return $this->notFound(
                        message: 'Discount must be between 0-100'
                    );
                }
            }
            //checking if the coupon is_discount_in_percent is 0 then the discount must be greater than 0
            if ($validated['is_discount_in_percent'] == 0) {
                if ($validated['discount'] <= 0) {
                    return $this->notFound(
                        message: 'Discount must be greater than 0'
                    );
                }
            }

            $coupon = new Coupon();
            $coupon->name = $validated['name'];
            $coupon->code = $validated['code'];
            $coupon->description = $validated['description'];
            $coupon->limit_per_use = $validated['limit_per_use'];
            $coupon->is_discount_in_percent = $validated['is_discount_in_percent'];
            $coupon->discount = $validated['discount'];
            $coupon->valid_from = $validated['valid_from'];
            $coupon->valid_till = $validated['valid_till'];
            $coupon->no_end_date = $validated['noEndDate'] == 'true' ? true : false;
            $coupon->num_of_coupons = $validated['num_of_coupons'];
            $coupon->conditions = $validated['conditions'];
            $coupon->is_active = true;
            $coupon->source_type = 'admin';
            $coupon->source_id = auth()->user()->id;
            $coupon->save();

            //adding coupon elegibility
            $couponElegibility = new CouponElegibility();
            $couponElegibility->coupon_id = $coupon->id;
            $couponElegibility->price_range_from = $validated['starting_price'];
            $couponElegibility->price_range_to = $validated['ending_price'];
            $couponElegibility->is_new_user_eligible = $validated['elegibility'] == 'new_user' ? true : false;
            $couponElegibility->is_all_users_eligible = $validated['elegibility'] == 'all_users' ? true : false;
            $couponElegibility->is_price_valid = $validated['elegibility'] == 'price' ? true : false;

            $couponElegibility->save();

            foreach (json_decode($validated['elegible_states']) as $stateId) {
                $coupon_valid_states = new CouponValidStates();
                $coupon_valid_states->coupon_id = $coupon->id;
                $coupon_valid_states->state_id = $stateId;
                $coupon_valid_states->save();
            }

            DB::commit();

            return $this->success(
                message: 'Coupon Successfully Created!'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->internalError($e->getMessage());
        }
    }

    public function editAdminCoupon(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'coupon_id' => 'required|exists:coupons,id',
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
                'description' => 'required|string',
                'limit_per_use' => 'required|integer',
                'is_discount_in_percent' => 'required',
                'discount' => 'required|integer',
                'elegibility' => 'required|string',
                'starting_price' => 'nullable|integer',
                'ending_price' => 'nullable|integer',
                'valid_from' => 'required|date',
                'valid_till' => 'required|date',
                'noEndDate' => 'nullable',
                'elegible_states' => 'required',
                'is_num_of_coupons' => 'nullable',
                'num_of_coupons' => 'nullable|integer',
                'conditions' => 'required|string',
            ]);

            //checking if the coupon is_discount_in_percent is 1 then the discount must be between 0-100
            if ($validated['is_discount_in_percent'] == 1) {
                if ($validated['discount'] < 0 || $validated['discount'] > 100) {
                    return $this->notFound(
                        message: 'Discount must be between 0-100'
                    );
                }
            }
            //checking if the coupon is_discount_in_percent is 0 then the discount must be greater than 0
            if ($validated['is_discount_in_percent'] == 0) {
                if ($validated['discount'] <= 0) {
                    return $this->notFound(
                        message: 'Discount must be greater than 0'
                    );
                }
            }

            $coupon = Coupon::where('id', $validated['coupon_id'])->first();
            $coupon->name = $validated['name'];
            $coupon->code = $validated['code'];
            $coupon->description = $validated['description'];
            $coupon->limit_per_use = $validated['limit_per_use'];
            $coupon->is_discount_in_percent = $validated['is_discount_in_percent'];
            $coupon->discount = $validated['discount'];
            $coupon->valid_from = $validated['valid_from'];
            $coupon->valid_till = $validated['valid_till'];
            $coupon->no_end_date = $validated['noEndDate'] == 'true' ? true : false;
            $coupon->num_of_coupons = $validated['num_of_coupons'];
            $coupon->conditions = $validated['conditions'];
            $coupon->is_active = true;
            $coupon->update();

            //adding coupon elegibility
            $couponElegibility = CouponElegibility::where('coupon_id', $coupon->id)->first();
            $couponElegibility->price_range_from = $validated['starting_price'];
            $couponElegibility->price_range_to = $validated['ending_price'];
            $couponElegibility->is_new_user_eligible = $validated['elegibility'] == 'new_user' ? true : false;
            $couponElegibility->is_all_users_eligible = $validated['elegibility'] == 'all_users' ? true : false;
            $couponElegibility->is_price_valid = $validated['elegibility'] == 'price' ? true : false;

            $couponElegibility->update();

            CouponValidStates::where('coupon_id', $coupon->id)->delete();

            foreach (json_decode($validated['elegible_states']) as $stateId) {
                $coupon_valid_states = new CouponValidStates();
                $coupon_valid_states->coupon_id = $coupon->id;
                $coupon_valid_states->state_id = $stateId;
                $coupon_valid_states->save();
            }

            DB::commit();

            return $this->success(
                message: 'Coupon Successfully Updated!'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->internalError($e->getMessage());
        }
    }

    public function addPropertyCoupon(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
                'description' => 'required|string',
                'limit_per_use' => 'required|integer',
                'is_discount_in_percent' => 'required',
                'discount' => 'required|integer',
                'starting_price' => 'nullable|integer',
                'ending_price' => 'nullable|integer',
                'valid_from' => 'required|date',
                'valid_till' => 'required|date',
                'noEndDate' => 'nullable',
                'elegibility' => 'required',
                'elegible_hotels' => 'required',
                'is_num_of_coupons' => 'nullable',
                'num_of_coupons' => 'nullable|integer',
                'conditions' => 'required|string',
            ]);

            //checking if the coupon is_discount_in_percent is 1 then the discount must be between 0-100
            if ($validated['is_discount_in_percent'] == 1) {
                if ($validated['discount'] < 0 || $validated['discount'] > 100) {
                    return $this->notFound(
                        message: 'Discount must be between 0-100'
                    );
                }
            }
            //checking if the coupon is_discount_in_percent is 0 then the discount must be greater than 0
            if ($validated['is_discount_in_percent'] == 0) {
                if ($validated['discount'] <= 0) {
                    return $this->notFound(
                        message: 'Discount must be greater than 0'
                    );
                }
            }

            $coupon = new Coupon();
            $coupon->name = $validated['name'];
            $coupon->code = $validated['code'];
            $coupon->description = $validated['description'];
            $coupon->limit_per_use = $validated['limit_per_use'];
            $coupon->is_discount_in_percent = $validated['is_discount_in_percent'];
            $coupon->discount = $validated['discount'];
            $coupon->valid_from = $validated['valid_from'];
            $coupon->valid_till = $validated['valid_till'];
            $coupon->no_end_date = $validated['noEndDate'] == 'true' ? true : false;
            $coupon->num_of_coupons = $validated['num_of_coupons'];
            $coupon->conditions = $validated['conditions'];
            $coupon->is_active = true;
            $coupon->source_type = 'vendor';
            $coupon->source_id = auth()->user()->id;
            $coupon->save();

            //adding coupon elegibility
            $couponElegibility = new CouponElegibility();
            $couponElegibility->coupon_id = $coupon->id;
            $couponElegibility->price_range_from = $validated['starting_price'];
            $couponElegibility->price_range_to = $validated['ending_price'];
            $couponElegibility->is_first_booking = $validated['elegibility'] == 'first_user' ? true : false;
            $couponElegibility->is_all_users_eligible = $validated['elegibility'] == 'all_users' ? true : false;
            $couponElegibility->is_price_valid = $validated['elegibility'] == 'price' ? true : false;

            $couponElegibility->save();

            foreach (json_decode($validated['elegible_hotels']) as $propertyId) {
                $coupon_valid_properties = new CouponValidProperties();
                $coupon_valid_properties->coupon_id = $coupon->id;
                $coupon_valid_properties->property_id = $propertyId;
                $coupon_valid_properties->save();
            }

            DB::commit();

            return $this->success(
                message: 'Coupon Successfully Created!'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->internalError($e->getMessage());
        }
    }

    public function editPropertyCoupon(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'coupon_id' => 'required|exists:coupons,id',
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
                'description' => 'required|string',
                'limit_per_use' => 'required|integer',
                'is_discount_in_percent' => 'required',
                'discount' => 'required|integer',
                'elegibility' => 'required|string',
                'starting_price' => 'nullable|integer',
                'ending_price' => 'nullable|integer',
                'valid_from' => 'required|date',
                'valid_till' => 'required|date',
                'noEndDate' => 'nullable',
                'elegible_hotels' => 'required',
                'is_num_of_coupons' => 'nullable',
                'num_of_coupons' => 'nullable|integer',
                'conditions' => 'required|string',
            ]);

            //checking if the coupon is_discount_in_percent is 1 then the discount must be between 0-100
            if ($validated['is_discount_in_percent'] == 1) {
                if ($validated['discount'] < 0 || $validated['discount'] > 100) {
                    return $this->notFound(
                        message: 'Discount must be between 0-100'
                    );
                }
            }
            //checking if the coupon is_discount_in_percent is 0 then the discount must be greater than 0
            if ($validated['is_discount_in_percent'] == 0) {
                if ($validated['discount'] <= 0) {
                    return $this->notFound(
                        message: 'Discount must be greater than 0'
                    );
                }
            }

            $coupon = Coupon::where('id', $validated['coupon_id'])->first();
            $coupon->name = $validated['name'];
            $coupon->code = $validated['code'];
            $coupon->description = $validated['description'];
            $coupon->limit_per_use = $validated['limit_per_use'];
            $coupon->is_discount_in_percent = $validated['is_discount_in_percent'];
            $coupon->discount = $validated['discount'];
            $coupon->valid_from = $validated['valid_from'];
            $coupon->valid_till = $validated['valid_till'];
            $coupon->no_end_date = $validated['noEndDate'] == 'true' ? true : false;
            $coupon->num_of_coupons = $validated['num_of_coupons'];
            $coupon->conditions = $validated['conditions'];
            $coupon->is_active = true;
            $coupon->update();

            //adding coupon elegibility
            $couponElegibility = CouponElegibility::where('coupon_id', $coupon->id)->first();
            $couponElegibility->price_range_from = $validated['starting_price'];
            $couponElegibility->price_range_to = $validated['ending_price'];
            $couponElegibility->is_first_booking = $validated['elegibility'] == 'first_user' ? true : false;
            $couponElegibility->is_all_users_eligible = $validated['elegibility'] == 'all_users' ? true : false;
            $couponElegibility->is_price_valid = $validated['elegibility'] == 'price' ? true : false;

            $couponElegibility->update();

            CouponValidProperties::where('coupon_id', $coupon->id)->delete();

            foreach (json_decode($validated['elegible_hotels']) as $propertyId) {
                $coupon_valid_properties = new CouponValidProperties();
                $coupon_valid_properties->coupon_id = $coupon->id;
                $coupon_valid_properties->property_id = $propertyId;
                $coupon_valid_properties->save();
            }

            DB::commit();

            return $this->success(
                message: 'Coupon Successfully Updated!'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->internalError($e->getMessage());
        }
    }
}
