<?php

namespace App\Http\Controllers\v1\admin;

use App\Exports\ExportVendorBankDetails;
use App\Exports\vendor\VendorsExport;
use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Jobs\RejectVendorBankDetailsRequest;
use App\Jobs\vendor\ApprovedVendorBankDetails;
use App\Jobs\vendor\WelcomeVendorMail;
use App\Mail\vendor\welcome;
use App\Models\PropertyPolicy;
use App\Models\RequestVendorBankDetails;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBankDetails;
use App\Notifications\vendor\WelcomeVendor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Ognjen\Laravel\AsyncMail;
use Snowfire\Beautymail\Beautymail;
use Illuminate\Support\Facades\Auth;


class VendorController extends Controller
{
    use HttpResponse;

    public function index()
    {
        try {
            $search = request()->query('search');
            $vendors = Vendor::where('name', 'LIKE', "%{$search}%")
                ->with('more_info')
                ->paginate(15);

            foreach ($vendors as $vendor) {
                if ($vendor->more_info->image && $vendor->more_info->image != "null") {
                    if (!filter_var($vendor->more_info->image, FILTER_VALIDATE_URL)) {
                        $image = asset('storage/user/image/' . $vendor->more_info->image);
                        $vendor->more_info->image = $image;
                    }
                }
            }

            return $this->success(
                data: $vendors
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function downloadVendorDetails(Vendor $vendor){
        try{
            if(!$vendor){
                return $this->error(
                    message: 'Vendor not found!'
                );
            }

            return Excel::download(new VendorsExport($vendor->vendor_id, 0), 'vendor_details_' . $vendor->vendor_id . '.xlsx');
        }catch (\Throwable $e){
            return $this->internalError($e->getMessage());
        }
    }

    public function downloadAllVendorsDetails(){
        try{
            return Excel::download(new VendorsExport(), 'all_vendors_details.xlsx');
        }catch (\Throwable $e){
            return $this->internalError($e->getMessage());
        }
    }

    public function showPolicies()
    {
        try {
            $search = request()->query('search');
            $policies = PropertyPolicy::where('policy', 'LIKE', "%{$search}%")
                ->where('vendor_id', Auth::user()->getVendor()->id)
                ->paginate(15);

            return $this->success(
                data: $policies
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function showAllPolicies()
    {
        try {
            $policies = PropertyPolicy::all();

            return $this->success(
                data: $policies
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addPolicy(Request $request)
    {
        try {
            $validated = $request->validate([
                'policy' => 'required|string'
            ]);

            $policy = new PropertyPolicy();
            $policy->vendor_id = Auth::user()->getVendor()->id;
            $policy->policy = $validated['policy'];
            if ($policy->save()) {
                return $this->success(
                    message: 'Policy Successfully Added!'
                );
            }
            return $this->error(
                message: 'Failed to add Policy!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deletePolicy(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|numeric|exists:property_policies,id'
            ]);

            $policy = PropertyPolicy::where('id', $validated['id'])->first();
            if ($policy->delete()) {
                return $this->success(
                    message: 'Policy Successfully Deleted!'
                );
            }
            return $this->error(
                message: 'Failed to delete Policy!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function isAccountDeactive()
    {
        try {
            $vendor = Vendor::where('user_id', Auth::user()->id)->first();

            if ($vendor->update()) {
                return $this->success(
                    data: $vendor->is_active,
                    message: "Activation Details"
                );
            }

            return $this->error(
                message: 'Failed to fetch account details!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function accountActivateDisable()
    {
        try {
            $vendor = Vendor::where('user_id', Auth::user()->id)->first();
            $vendor->is_active = !$vendor->is_active;

            if ($vendor->update()) {
                if ($vendor->is_active) {
                    return $this->success(
                        message: 'Account Successfully Activated!'
                    );
                }
                return $this->success(
                    message: 'Account Successfully Deactivated!'
                );
            }

            return $this->error(
                message: 'Failed to update account status!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function downloadVendorBankDetails(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required'
            ]);

            $vendorRequest = VendorBankDetails::where('id', $validated['request_id'])->first();

            if (!$vendorRequest) {
                return $this->error(
                    message: 'Request not found!'
                );
            }

            $requestData = [
                'id' => $vendorRequest->id,
                'is_request' => false
            ];

            return Excel::download(new ExportVendorBankDetails($requestData), 'bank_details_' . $vendorRequest->id . '.xlsx');

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function downloadVendorBankDetailsRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required'
            ]);

            $vendorRequest = RequestVendorBankDetails::where('id', $validated['request_id'])->first();

            if (!$vendorRequest) {
                return $this->error(
                    message: 'Request not found!'
                );
            }

            $requestData = [
                'id' => $vendorRequest->id,
                'is_request' => false
            ];
            return Excel::download(new ExportVendorBankDetails($requestData), 'bank_details_' . $vendorRequest->id . '.xlsx');

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function authAccountDetails()
    {
        try {
            $authDetails = Auth::user()->getVendor();

            $accountDetails = VendorBankDetails::where('vendor_id', $authDetails->id)->first();

            return $this->success(
                data: $accountDetails
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function rejectBankDetailsRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|numeric'
            ]);

            $request = RequestVendorBankDetails::where('id', $validated['request_id'])->first();

            $email = Vendor::where('id', $request->vendor_id)->first()->email;

            if ($request->delete()) {
                //sending mail to vendor
                dispatch(new RejectVendorBankDetailsRequest($email));
                return $this->success(
                    message: 'Bank Details Successfully Rejected!'
                );
            }

            return $this->error(
                message: 'Failed to reject Bank Details!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function approveBankDetailsRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|numeric',
            ]);

            $request = RequestVendorBankDetails::where('id', $validated['request_id'])->first();

            //vendor bank details already exists
            if (VendorBankDetails::where('vendor_id', $request->vendor_id)->exists()) {
                $vendor_bank_details = VendorBankDetails::where('vendor_id', $request->vendor_id)->first();
                $vendor_bank_details->account_name = $request->account_name;
                $vendor_bank_details->account_email = $request->account_email;
                $vendor_bank_details->ifsc_code = $request->ifsc_code;
                $vendor_bank_details->account_number = $request->account_number;

                if ($vendor_bank_details->update()) {
                    $vendor = Vendor::where('id', $request->vendor_id)->first();
                    $vendor->is_active = 1;
                    if ($vendor->update()) {
                        if ($request->delete()) {
                            //sending mail to vendor
                            dispatch(new ApprovedVendorBankDetails($vendor->email));
                            return $this->success(
                                message: 'Bank Details Successfully Approved!'
                            );
                        }
                    }
                }
            }


            $vendor_bank_details = new VendorBankDetails();
            $vendor_bank_details->vendor_id = $request->vendor_id;
            $vendor_bank_details->account_name = $request->account_name;
            $vendor_bank_details->account_email = $request->account_email;
            $vendor_bank_details->ifsc_code = $request->ifsc_code;
            $vendor_bank_details->account_number = $request->account_number;

            if ($vendor_bank_details->save()) {
                $vendor = Vendor::where('id', $request->vendor_id)->first();
                $vendor->is_active = 1;
                if ($vendor->update()) {
                    if ($request->delete()) {
                        //sending mail to vendor
                        dispatch(new ApprovedVendorBankDetails($vendor->email));
                        return $this->success(
                            message: 'Bank Details Successfully Approved!'
                        );
                    }
                }
            }

            return $this->error(
                message: 'Failed to approve Bank Details!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }


    public function updateVendorRazorpayId(Request $request)
    {
        try {
            $validated = $request->validate([
                'razorpayId' => 'required',
                'vendor_id' => 'required'
            ]);

            $vendor = Vendor::where('vendor_id', $validated['vendor_id'])->first();
            $vendor->razorpay_id = $validated['razorpayId'];

            if ($vendor->update()) {
                return $this->success(
                    message: 'Razorpay Id Successfully Updated!'
                );
            }

            return $this->error(
                message: 'Failed to update Razorpay Id!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function vendorAccounts()
    {
        try {
            $search = request()->query('search');
            $accounts = VendorBankDetails::where('account_email', 'LIKE', "%{$search}%")
                ->with('vendor')
                ->with('vendorDetails')
                ->paginate(15);

            return $this->success(
                data: $accounts
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function accountsRequests()
    {
        try {
            $search = request()->query('search');
            $accounts = RequestVendorBankDetails::where('account_email', 'LIKE', "%{$search}%")
                ->with('vendor')
                ->paginate(15);

            foreach ($accounts as $account) {
                $account->vendor_id = Vendor::where('id', $account->vendor_id)->first()->vendor_id;
            }

            return $this->success(
                data: $accounts
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getAuthVendor()
    {
        try {
            $vendor = Vendor::where('user_id', Auth::user()->id)->first();

            //checking if vendor has filled his bank details
            if (RequestVendorBankDetails::where('vendor_id', $vendor->id)->exists()) {
                $vendor->is_bank_details = 1;
            } else {
                $vendor->is_bank_details = 0;
            }

            if ($vendor) {
                return $this->success(
                    data: $vendor
                );
            }

            return $this->error(
                message: 'Failed to new Vendor!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }


    public function addBankDetails(Request $request)
    {
        try {
            $validated = $request->validate([
                'account_name' => 'required|string|min:3|max:255',
                'account_email' => 'required|string|email|max:255',
                'ifsc_code' => 'required|string|min:3|max:255',
                'account_number' => 'required|string|min:3|max:255',
            ]);

            $vendor_id = Vendor::where('user_id', Auth::user()->id)->first()->id;

            if (RequestVendorBankDetails::where('vendor_id', $vendor_id)->exists()) {
                return $this->error(
                    message: 'Bank Details already sent for approval!'
                );
            }

            $vendorRequest = new RequestVendorBankDetails();
            $vendorRequest->vendor_id = $vendor_id;
            $vendorRequest->account_name = $validated['account_name'];
            $vendorRequest->account_email = $validated['account_email'];
            $vendorRequest->ifsc_code = $validated['ifsc_code'];
            $vendorRequest->account_number = $validated['account_number'];

            if ($vendorRequest->save()) {
                return $this->success(
                    message: 'Bank Details Successfully sent for approval!'
                );
            }

            return $this->error(
                message: 'Failed to add send Details for approval!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }


    public function addVendor(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'address' => 'required|string',
                'gst_number' => 'required|string',
                'phone_number' => 'required|string',
                'email' => 'required|string|email',
                'password' => 'sometimes|string'
            ]);

            //Generating Unique Vendor ID
            do {
                $vendor_id = 'V-' . substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6);
            } while (Vendor::where('vendor_id', $vendor_id)->exists());

            //checking if that email is already registered as user
            if (User::where('email', $validated['email'])->exists()) {
                $user = User::where('email', $validated['email'])->first();

                $user->role = 1;
                if ($user->update()) {
                    $vendor = Vendor::create([
                        'user_id' => $user->id,
                        'name' => $validated['name'],
                        'address' => $validated['address'],
                        'vendor_id' => $vendor_id,
                        'gst_number' => $validated['gst_number'],
                        'phone_number' => $validated['phone_number'],
                        'email' => $validated['email'],
                    ]);

                    if ($vendor->save()) {

                        $vendorData = [
                            'vendor_id' => $vendor_id,
                            'email' => $vendor->email,
                            'password' => $vendor->phone_number,
                        ];
                        //sending mail to vendor
                        dispatch(new WelcomeVendorMail($vendorData));

                        return $this->success(
                            message: 'Vendor Successfully Added! with Id: ' . $vendor_id
                        );
                    }
                } else {
                    return $this->error(
                        message: 'Failed to new Vendor!'
                    );
                }
            } else {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'] ? bcrypt($validated['password']) : bcrypt($validated['phone_number']),
                    'address' => $validated['address'],
                    'phone_number' => $validated['phone_number'],
                    'role' => 1
                ]);

                if ($user->save()) {
                    $vendor = Vendor::create([
                        'user_id' => $user->id,
                        'name' => $validated['name'],
                        'address' => $validated['address'],
                        'vendor_id' => $vendor_id,
                        'gst_number' => $validated['gst_number'],
                        'phone_number' => $validated['phone_number'],
                        'email' => $validated['email'],
                    ]);

                    if ($vendor->save()) {

                        $vendorData = [
                            'vendor_id' => $vendor_id,
                            'email' => $vendor->email,
                            'password' => $vendor->phone_number,
                        ];
                        //sending mail to vendor
                        dispatch(new WelcomeVendorMail($vendorData));

                        // Mail::to('vermamanav117@gmail.com')->queue(new SendCodeResetPassword('123456'));
                        $user->notify(new WelcomeVendor($vendorData));
                        return $this->success(
                            message: 'Vendor Successfully Added! with Id: ' . $vendor_id
                        );
                    }
                }
            }

            return $this->error(
                message: 'Failed to new Vendor!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function editVendor(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric',
                'name' => 'required|string',
                'address' => 'required|string',
                'vendor_id' => 'required|string',
                'gst_number' => 'required|string',
                'phone_number' => 'required|string',
                'email' => 'required|string|email',
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $vendor = Vendor::where('id', $request->id)->first();
            $vendor->name = $request->name;
            $vendor->address = $request->address;
            $vendor->vendor_id = $request->vendor_id;
            $vendor->gst_number = $request->gst_number;
            $vendor->phone_number = $request->phone_number;
            $vendor->email = $request->email;

            if ($vendor->update()) {
                return $this->success(
                    message: 'Vendor Successfully Updated!'
                );
            }
            return $this->error(
                message: 'Failed to update Vendor!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deleteVendor(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric'
            ]);
            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );
            $vendor = Vendor::where('id', $request->id)->first();

            //Getting vendor's user id
            $user_id = $vendor->user_id;
            //Delete vendor's user
            $user = User::where('id', $user_id)->first();
            $user->delete();
            if ($vendor->delete()) {
                return $this->success(
                    message: 'Vendor Successfully Deleted!'
                );
            } else {
                return $this->error(
                    message: 'Failed to delete Vendor!'
                );
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function blockVendor(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'vendor_id' => 'required|string',
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $vendor = Vendor::where('id', $request->vendor_id)->firstOrFail();
            $user = User::where('id', $vendor->user_id)->firstOrFail();
            $user->is_blocked = !$user->is_blocked;

            if ($user->update()) {
                return $this->success(
                    message: 'Vendor Successfully ' . ($user->is_blocked ? 'Blocked!' : 'Unblocked!')
                );
            } else {
                return $this->error(
                    message: 'Failed to ' . ($user->is_blocked ? 'Blocked!' : 'Unblocked!') . ' Vendor!'
                );
            }

        } catch (\Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->notFound(
                    message: 'Vendor not found!'
                );
            }
            return $this->internalError($e->getMessage());
        }
    }

}
