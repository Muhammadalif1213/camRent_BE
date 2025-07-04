<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Camera;
use Illuminate\Support\Facades\Validator;

class CameraController extends Controller
{
    /**
     * Menampilkan daftar semua kamera.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Ambil semua data kamera, diurutkan dari yang terbaru
            $cameras = Camera::latest()->get();

            return response()->json([
                'message' => 'Data kamera berhasil diambil',
                'data' => $cameras
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data kamera.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data kamera baru ke database.
     *
     * @bodyParam name string required Nama kamera. Example: Sony A7 III
     * @bodyParam brand string required Merek kamera. Example: Sony
     * @bodyParam description string required Deskripsi dan spesifikasi.
     * @bodyParam rental_price_per_day number required Harga sewa per hari. Example: 250000
     * @bodyParam image_url string required URL gambar kamera.
     */
    public function store(Request $request)
    {
        // Validasi input dari request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'description' => 'required|string',
            'rental_price_per_day' => 'required|numeric|min:0',
            'image_url' => 'required|string|url', // Validasi sebagai URL
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buat dan simpan data kamera baru
        $camera = Camera::create($validator->validated());

        // Beri response sukses
        return response()->json([
            'message' => 'Data kamera berhasil ditambahkan',
            'data' => $camera
        ], 201); // 201 Created
    }

    /**
     * Memperbarui data kamera yang sudah ada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Camera  $camera
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Camera $camera)
    {
        // Validasi input, sama seperti saat membuat data baru
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'description' => 'required|string',
            'rental_price_per_day' => 'required|numeric|min:0',
            'image_url' => 'required|string|url',
            'status' => 'required|in:available,rented,maintenance', // Tambahkan validasi status
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update data kamera dengan data yang tervalidasi
        $camera->update($validator->validated());

        // Beri response sukses dengan data yang sudah diperbarui
        return response()->json([
            'message' => 'Data kamera berhasil diperbarui',
            'data' => $camera
        ], 200); // 200 OK
    }

    /**
     * Menghapus data kamera dari database.
     *
     * @param \App\Models\Camera $camera
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Camera $camera)
    {
        try {
            // Hapus data kamera
            $camera->delete();

            // Beri response sukses
            return response()->json([
                'message' => 'Data kamera berhasil dihapus'
            ], 200); // 200 OK

        } catch (\Exception $e) {
            // Tangani jika ada error lain
            return response()->json([
                'message' => 'Gagal menghapus data kamera.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail satu kamera spesifik.
     *
     * @param  \App\Models\Camera  $camera
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Camera $camera)
    {
        // Karena kita menggunakan Route Model Binding, Laravel sudah otomatis
        // mencari kamera berdasarkan ID dari URL. Jika tidak ada, ia akan
        // otomatis mengembalikan error 404 Not Found.

        return response()->json([
            'message' => 'Data detail kamera berhasil diambil',
            'data' => $camera
        ], 200);
    }

}
