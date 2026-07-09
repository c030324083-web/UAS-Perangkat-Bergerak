<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\JenisBuku;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PerpustakaanController extends Controller
{
    // ==========================================
    // 1. FITUR AUTENTIKASI (FR-01)
    // ==========================================
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan salah.'],
            ]);
        }

        // Buat token Sanctum mencakup role user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user // Mengembalikan id, name, email, dan role custom
        ]);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil dari sistem'
        ]);
    }

    // ==========================================
    // 2. FITUR KATALOG & EKSPLORASI (FR-05 & FR-06)
    // ==========================================
    
    public function index(Request $request)
    {
        $query = Buku::with('jenisBuku');

        // Filter Pencarian Judul/Kata Kunci (FR-05)
        if ($request->has('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // Filter Berdasarkan Jenis Buku / Kategori (FR-05)
        if ($request->has('jenis_id')) {
            $query->where('jenis_buku_id', $request->jenis_id);
        }

        $buku = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $buku
        ]);
    }

    public function show($id)
    {
        $buku = Buku::with('jenisBuku')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $buku // Menampilkan detail lengkap & sinopsis (FR-06)
        ]);
    }

    // ==========================================
    // 3. FITUR MANAJEMEN BUKU - PETUGAS (FR-02 & FR-04)
    // ==========================================
    
    public function store(Request $request)
    {
        // Proteksi Validasi Backend (Validasi keunikan kode_buku - FR-04)
        $validated = $request->validate([
            'kode_buku' => 'required|string|unique:bukus,kode_buku',
            'judul' => 'required|string|max:255',
            'jenis_buku_id' => 'required|exists:jenis_bukus,id',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'sinopsis' => 'required|string',
        ]);

        $buku = Buku::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data buku berhasil disimpan ke database pusat',
            'data' => $buku
        ], 201);
    }
}