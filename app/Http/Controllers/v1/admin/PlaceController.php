<?php

namespace App\Http\Controllers\v1\admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\City;
use App\Models\State;
use App\Services\ImageUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlaceController extends Controller
{
    use HttpResponse;

    public function addState(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $state = new State();
            $state->name = $request->name;

            if ($request->hasFile('image')) {
                $imageService = new ImageUploadService();
                $image = $imageService->uploadImage($request->file('image'), '/places/states/');
                $state->image = $image;
            } else {
                return $this->error(
                    message: 'Failed to new State!'
                );
            }

            if ($state->save()) {
                return $this->success(
                    message: 'State Added Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to new State!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deleteState(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric'
            ]);
            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );
            $state = State::where('id', $request->id)->firstOrFail();

            //delete all state with this city id
            $cities = City::where('state_id', $state->id)->get();
            foreach ($cities as $city) {
                $this->deleteCity(new Request(['id' => $city->id]));
            }

            //deleting state image
            if ($state->image) {
                $imageService = new ImageUploadService();
                $imageService->deleteImage($state->image, '/places/states/');
            }

            if ($state->delete()) {
                return $this->success(
                    message: 'State Successfully Deleted!'
                );
            } else {
                return $this->error(
                    message: 'Failed to delete State!'
                );
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function editState(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $state = State::find($request->id);
            $state->name = $request->name;

            if ($request->hasFile('image')) {
                $imageService = new ImageUploadService();
                $image = $imageService->updateImage($state->image, $request->file('image'), '/places/states/');
                $state->image = $image;
            }

            if ($state->update()) {
                return $this->success(
                    message: 'State Successfully updated!'
                );
            }
            return $this->error(
                message: 'Failed to update State!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addCity(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'state_id' => 'required|numeric',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $city = new City();
            $city->name = $request->name;
            $city->state_id = $request->state_id;

            if ($request->hasFile('image')) {
                $imageService = new ImageUploadService();
                $image = $imageService->uploadImage($request->file('image'), '/places/cities/');
                $city->image = $image;
            } else {
                return $this->error(
                    message: 'Failed to new City!'
                );
            }

            if ($city->save()) {
                return $this->success(
                    message: 'City Created Successfully!'
                );
            }
            return $this->error(
                message: 'Failed to new City!'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function editCity(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required|string|max:255',
                'state_id' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $city = City::where('id', $request->id)->first();
            $city->name = $request->name;
            $city->state_id = $request->state_id;

            if ($request->hasFile('image')) {
                $imageService = new ImageUploadService();
                $image = $imageService->updateImage($city->image, $request->file('image'), '/places/cities/');
                $city->image = $image;
            }

            if ($city->update()) {
                return $this->success(
                    message: 'City Successfully updated!'
                );
            }
            return $this->error(
                message: 'Failed to update City!'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function stopCity(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'city_id' => 'required|string',
            ]);

            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );

            $city = City::where('id', $request->city_id)->firstOrFail();
            $city->is_stopped = !$city->is_stopped;

            if ($city->update()) {
                return $this->success(
                    message: 'City ' . ($city->is_stopped ? 'Services Stopped!' : 'Services Started!')
                );
            } else {
                return $this->error(
                    message: 'Failed to ' . ($city->is_stopped ? 'Stop Services!' : 'Start Services!')
                );
            }

        } catch (\Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                return $this->notFound(
                    message: 'City not found!'
                );
            }
            return $this->internalError($e->getMessage());
        }
    }

    public function deleteCity(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric'
            ]);
            if ($validator->fails())
                return $this->error(
                    message: 'Invalid Request!'
                );
            $city = City::where('id', $request->id)->firstOrFail();

            //deleting city image
            if ($city->image) {
                $imageService = new ImageUploadService();
                $imageService->deleteImage($city->image, '/places/cities/');
            }

            if ($city->delete()) {
                return $this->success(
                    message: 'City Successfully Deleted!'
                );
            } else {
                return $this->error(
                    message: 'Failed to delete City!'
                );
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
