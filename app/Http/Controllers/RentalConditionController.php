<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RentalConditionController extends Controller
{
    /**
     * Admin merekam kondisi kamera saat pengambilan atau pengembalian.
     */
    public function store(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'camera_id' => 'required|exists:cameras,id',
            'type' => 'required|in:pickup,return',
            'notes' => 'nullable|string',
            'condition_photo' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $path = $request->file('condition_photo')->store('condition_photos', 'public');

        $booking->rentalConditions()->create([
            'camera_id' => $request->camera_id,
            'type' => $request->type,
            'notes' => $request->notes,
            'photo_path' => $path,
            'checked_by_admin_id' => Auth::id(),
        ]);

        if ($request->type === 'return') {
            $camera = Camera::find($request->camera_id);
            $camera->update(['status' => 'available']);
        }

        return response()->json(['message' => 'Kondisi kamera saat '.$request->type.' berhasil direkam.'], 200);
    }
}
