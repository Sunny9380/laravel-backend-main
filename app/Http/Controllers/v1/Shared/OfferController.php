<?php

namespace App\Http\Controllers\v1\Shared;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\Offer;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    use HttpResponse;

    public function deleteOffer(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:offers,id'
            ]);

            $offer = Offer::find($validated['id']);

            // deleting offer image
            $imageService = new ImageUploadService();
            $imageService->deleteImage($offer->background_image, '/offers/');

            if ($offer->delete()) {
                return $this->success(
                    message: 'Offer deleted successfully'
                );
            }

            return $this->error(
                message: 'Failed to delete offer'
            );

        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function toggleOfferStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:offers,id',
            ]);

            $offer = Offer::find($validated['id']);
            $offer->is_active = !$offer->is_active;
            if ($offer->save()) {
                return $this->success(
                    message: 'Offer status updated successfully'
                );
            }

            return $this->error(
                message: 'Failed to update offer status'
            );

        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getAllOffersForAdmin()
    {
        try {
            $search = request()->query('search');
            $offers = Offer::orderBy('created_at', 'desc')
                ->where('heading', 'LIKE', "%$search%")
                ->paginate(15);

            foreach ($offers as $offer) {
                $offer->background_image = asset('storage/offers/' . $offer->background_image);
            }
            return $this->success(
                data: $offers
            );
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getOffers()
    {
        try {
            $offers = Offer::
                where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where('is_active', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($offers as $offer) {
                $offer->background_image = asset('storage/offers/' . $offer->background_image);
            }
            return $this->success(
                data: $offers
            );
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addOffer(Request $request)
    {
        try {
            $validated = $request->validate([
                'heading' => 'required|string|max:50',
                'description' => 'required|string',
                'background_image' => 'required|image|max:2048',
                'is_coupon_code' => 'required',
                'coupon_code' => 'nullable|string|max:50',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);

            $offer = new Offer();
            $offer->heading = $validated['heading'];
            $offer->description = $validated['description'];
            $offer->is_coupon_code = $validated['is_coupon_code'];
            $offer->coupon_code = $validated['coupon_code'];
            $offer->start_date = $validated['start_date'];
            $offer->end_date = $validated['end_date'];

            if ($request->hasFile('background_image')) {
                $imageService = new ImageUploadService();
                $offer->background_image = $imageService->uploadImage($request->file('background_image'), '/offers/');
            }

            if ($offer->save()) {
                return $this->success(
                    message: 'Offer added successfully'
                );
            }

            return $this->error(
                message: 'Failed to add offer'
            );

        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
    public function updateOffer(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:offers,id',
                'heading' => 'required|string|max:50',
                'description' => 'required|string',
                'background_image' => 'nullable|max:2048',
                'is_coupon_code' => 'required',
                'coupon_code' => 'nullable|string|max:50',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);

            $offer = Offer::find($validated['id']);

            if (!$offer) {
                return $this->error(
                    message: 'Offer not found'
                );
            }

            $offer->heading = $validated['heading'];
            $offer->description = $validated['description'];
            $offer->is_coupon_code = $validated['is_coupon_code'];
            $offer->coupon_code = $validated['coupon_code'];
            $offer->start_date = $validated['start_date'];
            $offer->end_date = $validated['end_date'];

            if ($request->hasFile('background_image')) {
                // deleting old image
                if ($offer->background_image) {
                    $imageService = new ImageUploadService();
                    $imageService->deleteImage($offer->background_image, '/offers/');
                }
                $imageService = new ImageUploadService();
                $offer->background_image = $imageService->uploadImage($request->file('background_image'), '/offers/');
            }

            if ($offer->save()) {
                return $this->success(
                    message: 'Offer updated successfully'
                );
            }

            return $this->error(
                message: 'Failed to update offer'
            );

        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
