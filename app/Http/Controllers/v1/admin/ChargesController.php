<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\CompanyCharge;
use App\Models\Configuration;
use App\Models\TaxCharge;
use Illuminate\Http\Request;

class ChargesController extends Controller
{
    use HttpResponse;

    public function getTaxCharges()
    {
        try {
            $charges = TaxCharge::all();
            return $this->success(
                data: $charges
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getCompanyCharges()
    {
        try {
            $charges = CompanyCharge::all();

            return $this->success(
                data: $charges
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function deleteTaxCharge(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:tax_charges,id',
            ]);
            $charge = TaxCharge::find($validated['id']);
            if ($charge->delete()) {
                return $this->success(
                    message: 'Tax charge deleted successfully'
                );
            }

            return $this->error(
                message: 'Failed to delete tax charge'
            );

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function getRazorpayConfig()
    {
        try {
            $config = Configuration::select('razorpay_key', 'razorpay_secret')->first();
            return $this->success(
                data: $config
            );
        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function setRazpayConfig(Request $request)
    {
        try {
            $validated = $request->validate([
                'razorpayKey' => 'required|string',
                'razorpaySecret' => 'required|string',
            ]);

            $config = Configuration::first();
            $config->razorpay_key = $validated['razorpayKey'];
            $config->razorpay_secret = $validated['razorpaySecret'];
            if ($config->save()) {
                return $this->success(
                    message: 'Razorpay configuration updated successfully'
                );
            }

            return $this->error(
                message: 'Failed to update razorpay configuration'
            );

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function deleteCompanyCharge(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:company_charges,id',
            ]);
            $charge = CompanyCharge::find($validated['id']);
            if ($charge->delete()) {
                return $this->success(
                    message: 'Company charge deleted successfully'
                );
            }

            return $this->error(
                message: 'Failed to delete company charge'
            );

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function toggleTaxStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:tax_charges,id',
            ]);
            $charge = TaxCharge::find($validated['id']);
            $charge->is_active = !$charge->is_active;
            if ($charge->save()) {
                return $this->success(
                    message: 'Tax charge status updated successfully'
                );
            }

            return $this->error(
                message: 'Failed to update tax charge status'
            );

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function toggleCompanyChargeStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:company_charges,id',
            ]);
            $charge = CompanyCharge::find($validated['id']);
            $charge->is_active = !$charge->is_active;
            if ($charge->save()) {
                return $this->success(
                    message: 'Company charge status updated successfully'
                );
            }

            return $this->error(
                message: 'Failed to update company charge status'
            );

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addTaxCharge(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'charge' => 'required|numeric',
                'minValue' => 'required|numeric',
            ]);
            $charge = new TaxCharge();
            $charge->name = $validated['name'];
            $charge->charge = $validated['charge'];
            $charge->min_order_amount = $validated['minValue'];
            if ($charge->save()) {
                return $this->success(
                    message: 'Tax charge added successfully'
                );
            }

            return $this->error(
                message: 'Failed to add tax charge'
            );

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }

    public function addCompanyCharge(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'charge' => 'required|numeric',
                'is_percent' => 'required|boolean',
            ]);
            $charge = new CompanyCharge();
            $charge->name = $validated['name'];
            $charge->charge = $validated['charge'];
            $charge->is_percent = $validated['is_percent'];
            if ($charge->save()) {
                return $this->success(
                    message: 'Company charge added successfully'
                );
            }

            return $this->error(
                message: 'Failed to add Company charge'
            );

        } catch (\Exception $e) {
            return $this->error(
                message: $e->getMessage()
            );
        }
    }
}
