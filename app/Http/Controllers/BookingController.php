<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Camera;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Http\Requests\CompleteBookingDataRequest;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Membuat booking baru oleh customer.
     */
    public function store(StoreBookingRequest $request)
    {
        // Memulai transaksi untuk memastikan semua query berhasil atau semua dibatalkan.
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            

            $startDate = Carbon::parse($validatedData['start_date']);
            $endDate = Carbon::parse($validatedData['end_date']);
            $numberOfDays = $startDate->diffInDays($endDate) + 1;
            
            $totalPrice = 0;
            $itemsForPivot = [];

            foreach ($validatedData['items'] as $item) {
                $camera = Camera::findOrFail($item['camera_id']);

                if ($camera->status !== 'available') {
                    throw new Exception("Kamera '{$camera->name}' tidak tersedia.");
                }

                $price = $camera->rental_price_per_day * $item['quantity'] * $numberOfDays;
                $totalPrice += $price;

                $itemsForPivot[$item['camera_id']] = [
                    'quantity' => $item['quantity'],
                    'price_at_booking' => $camera->rental_price_per_day
                ];
            }

            $booking = Booking::create([
                'user_id'     => Auth::id(),
                'start_date'  => $validatedData['start_date'],
                'end_date'    => $validatedData['end_date'],
                'total_price' => $totalPrice,
                'status'      => 'pending',
            ]);

            $booking->cameras()->attach($itemsForPivot);
            
            DB::commit(); // Simpan semua perubahan jika tidak ada error

            return response()->json([
                'message' => 'Booking berhasil dibuat, menunggu persetujuan admin.', 
                'status_code' => 201,
                'data' => $booking->load('cameras')
            ], 201);

        } catch (Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error
            // statusCode 409 (Conflict) lebih cocok untuk error ketersediaan barang
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    /**
     * melengkapi data booking (lokasi & KTP).
     */
    public function completeData(CompleteBookingDataRequest $request, Booking $booking)
    {
        // 1. Pengecekan Keamanan: Pastikan user yang login adalah pemilik booking ini
        if (Auth::id() !== $booking->user_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // 2. Pengecekan Status: Pastikan hanya booking 'pending' yang bisa diisi
        if ($booking->status !== 'pending') {
            return response()->json(['message' => 'Data untuk booking ini sudah tidak bisa diubah.'], 409);
        }

        try {
            $validatedData = $request->validated();
            $idCardPath = $request->file('id_card_image')->store('id_cards', 'public');

            // Hapus gambar KTP lama jika ada
            if ($booking->id_card_image_path) {
                Storage::disk('public')->delete($booking->id_card_image_path);
            }

            // Update booking dengan data baru
            $booking->update([
                'location'           => $validatedData['location'],
                'id_card_image_path' => $idCardPath,
            ]);

            return response()->json([
                'message' => 'Data jaminan berhasil dilengkapi. Menunggu persetujuan admin.',
                'data' => $booking,
            ], 200);

        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Menampilkan daftar semua booking (untuk admin).
     */
    public function index()
    {
        try {

            $bookings = Booking::with('user', 'cameras')->latest()->get();

            return response()->json([
                'message' => 'Data semua booking berhasil diambil',
                'status_code' => 200,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data booking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengubah status booking oleh Admin.
     * Validasi ditangani oleh UpdateBookingStatusRequest.
     */
    public function updateStatus(UpdateBookingStatusRequest $request, Booking $booking)
    {
        try {
            $newStatus = $request->validated()['status'];

            // Update status bookingnya
            $booking->update(['status' => $newStatus]);
            
            // Dapatkan semua ID kamera yang ada di dalam booking ini
            $cameraIds = $booking->cameras()->pluck('cameras.id');

            // Logika untuk mengubah status kamera berdasarkan status booking baru
            if ($newStatus === 'approved' || $newStatus === 'ongoing') {
                Camera::whereIn('id', $cameraIds)->update(['status' => 'rented']);
            } elseif (in_array($newStatus, ['completed', 'rejected', 'cancelled'])) {
                // Jika booking selesai/dibatalkan/ditolak, kembalikan status kamera
                Camera::whereIn('id', $cameraIds)->update(['status' => 'available']);
            }

            return response()->json([
                'message' => "Status booking berhasil diubah menjadi '{$newStatus}'.",
                'data'    => $booking->load('user', 'cameras')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui status booking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus data booking oleh Admin.
     */
    public function destroy(Booking $booking)
    {
        try {
            // Hapus file bukti pembayaran dari storage jika ada
            if ($booking->payment_proof_path) {
                Storage::disk('public')->delete($booking->payment_proof_path);
            }

            // Hapus data booking dari database.
            // Relasi di tabel pivot (camera_booking) akan terhapus otomatis
            // karena kita menggunakan onDelete('cascade') di migrasi.
            $booking->delete();

            return response()->json([
                'message' => 'Booking berhasil dihapus.',
                'status_code' => 200,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus booking.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengambil riwayat booking hanya untuk pengguna yang sedang login.
     */
    public function myBookings()
    {
        try {
            // Mengambil ID dari pengguna yang terotentikasi melalui token
            $userId = Auth::id();

            // Mengambil semua booking milik user tersebut,
            // beserta data kamera terkait, diurutkan dari yang terbaru.
            $bookings = Booking::where('user_id', $userId)
                                ->with('cameras')
                                ->latest()
                                ->get();

            return response()->json([
                'message' => 'Riwayat booking berhasil diambil',
                'status_code' => 200,
                'data' => $bookings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil riwayat booking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
