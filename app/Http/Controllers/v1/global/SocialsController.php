<?php

namespace App\Http\Controllers\v1\global;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\Socials;
use Illuminate\Http\Request;

class SocialsController extends Controller
{
    use HttpResponse;

    public function getSocials()
    {
        try {
            $socials = Socials::first();
            return $this->success(
                data: $socials
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function updateSocials(Request $request)
    {
        try {
            $validated = $request->validate([
                'facebook' => 'nullable|string',
                'twitter' => 'nullable|string',
                'instagram' => 'nullable|string',
                'linkedin' => 'nullable|string',
                'youtube' => 'nullable|string',
            ]);

            $socials = Socials::first();

            if (!$socials) {
                $socials = new Socials();
            }

            $socials->facebook = $validated['facebook'] ?? null;
            $socials->twitter = $validated['twitter'] ?? null;
            $socials->instagram = $validated['instagram'] ?? null;
            $socials->linkedin = $validated['linkedin'] ?? null;
            $socials->youtube = $validated['youtube'] ?? null;

            if ($socials->save()) {
                return $this->success(
                    message: 'Socials added successfully'
                );
            }

            return $this->error(
                message: 'Failed to add socials'
            );

        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

}
