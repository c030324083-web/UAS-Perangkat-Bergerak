<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JenisBuku;

class JenisBukuController extends Controller
{
    /**
     * Mengambil semua jenis buku untuk Dropdown di mobile app
     */
    public function index()
    {
        $jenis = JenisBuku::all();

        return response()->json([
            'status' => 'success',
            'data' => $jenis
        ], 200);
    }
}