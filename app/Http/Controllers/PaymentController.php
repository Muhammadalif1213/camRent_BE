<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class PaymentController extends Controller
{
    /**
     * Admin merekam bukti pembayaran untuk sebuah booking.
     */
    public function store(Request $request, Booking $booking)
{
    try {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'payment_proof' => 'required|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Simpan gambar ke storage
        $file = $request->file('payment_proof');
        $path = $file->store('payment_proofs', 'public'); // disimpan di storage/app/public/payment_proofs

        // Simpan data payment
        $payment = $booking->payments()->create([
            'amount' => $request->amount,
            'proof_path' => $path,
            'confirmed_by_admin_id' => Auth::id(),
        ]);

        // Update status booking
        $booking->update(['payment_status' => 'paid']);

        // URL file yang dapat diakses publik
        $imageUrl = asset('storage/' . $path);

        return response()->json([
            'message' => 'Bukti pembayaran berhasil direkam.',
            'data' => [
                'id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'amount' => $payment->amount,
                'proof_url' => $imageUrl,
                'confirmed_by_admin_id' => $payment->confirmed_by_admin_id,
                'created_at' => $payment->created_at,
            ]
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan saat menyimpan pembayaran.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
