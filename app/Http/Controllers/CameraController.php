<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Camera;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\AddCameraRequest;

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
     * Menyimpan data kamera baru.
     */
    public function store(AddCameraRequest $request)
    {
        try {
            $binaryImage = $request->hasFile('foto_camera')
                ? $request->file('foto_camera')->get()
                : null;

            $camera = Camera::create([
                'name' => $request->name,
                'brand' => $request->brand,
                'description' => $request->description,
                'rental_price_per_day' => $request->rental_price_per_day,
                'status' => $request->status,
                'foto_camera' => $binaryImage,
            ]);

            return response()->json([
                'message' => 'Kamera berhasil ditambahkan',
                'data' => [
                    'id' => $camera->id,
                    'name' => $camera->name,
                    'brand' => $camera->brand,
                    'description' => $camera->description,
                    'rental_price_per_day' => $camera->rental_price_per_day,
                    'status' => $camera->status,
                    'foto_camera' => $binaryImage ? base64_encode($binaryImage) : null,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data kamera.',
                'error' => $e->getMessage()
            ], 500);
        }
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'description' => 'required|string',
            'rental_price_per_day' => 'required|numeric|min:0',
            'status' => 'required|in:available,rented,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // opsional update
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Konversi file gambar baru jika ada
        if ($request->hasFile('image')) {
            $binaryImage = file_get_contents($request->file('image'));
            $camera->foto_camera = $binaryImage;
        }

        $camera->update([
            'name' => $request->name,
            'brand' => $request->brand,
            'description' => $request->description,
            'rental_price_per_day' => $request->rental_price_per_day,
            'status' => $request->status,
            // foto_camera sudah diubah sebelumnya jika ada file baru
        ]);

        return response()->json([
            'message' => 'Data kamera berhasil diperbarui',
            'data' => [
                'id' => $camera->id,
                'name' => $camera->name,
                'brand' => $camera->brand,
                'description' => $camera->description,
                'rental_price_per_day' => $camera->rental_price_per_day,
                'status' => $camera->status,
                'foto_camera_base64' => base64_encode($camera->foto_camera),
            ]
        ], 200);
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
