<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BukuController extends Controller
{
    /**
     * FR-05: Menampilkan Katalog Buku + Fitur Pencarian & Filter Kategori (Untuk Anggota & Petugas)
     */
    public function index(Request $request)
    {
        $query = Buku::with('jenisBuku');

        // Fitur Pencarian berdasarkan Judul Buku
        if ($request->has('search') && $request->search != '') {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // Fitur Filter berdasarkan Jenis Buku (Kategori ID)
        if ($request->has('jenis_buku_id') && $request->jenis_buku_id != '') {
            $query->where('jenis_buku_id', $request->jenis_buku_id);
        }

        // Tampilkan data terbaru di atas
        $bukus = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Katalog buku berhasil diambil',
            'data' => $bukus
        ], 200);
    }

    /**
     * FR-02 & FR-04: Proses Menyimpan Buku Baru oleh Petugas (Dengan Validasi Pusat)
     */
    public function store(Request $request)
    {
        // Validasi ketat di sisi backend sesuai aturan Bab 8 di PRD
        $validated = $request->validate([
            'kode_buku' => 'required|string|unique:bukus,kode_buku', // Mengunci keunikan kode buku
            'judul' => 'required|string|max:255',
            'jenis_buku_id' => 'required|exists:jenis_bukus,id', // Memastikan jenis buku valid di DB
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'sinopsis' => 'required|string',
        ], [
            'kode_buku.unique' => 'Kode buku sudah terdaftar di sistem perpustakaan pusat.',
            'jenis_buku_id.exists' => 'Jenis buku yang dipilih tidak valid.'
        ]);

        $buku = Buku::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data buku berhasil disimpan ke database pusat',
            'data' => $buku->load('jenisBuku')
        ], 201);
    }

    /**
     * FR-06: Menampilkan Detail Buku & Sinopsis Lengkap (Untuk Anggota)
     */
    public function show($id)
    {
        $buku = Buku::with('jenisBuku')->find($id);

        if (!$buku) {
            return response()->json([
                'status' => 'error',
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail buku berhasil diambil',
            'data' => $buku
        ], 200);
    }

    /**
     * FR-02 (Update): Mengubah data buku berdasarkan ID (Khusus Petugas)
     */
    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        // Validasi keunikan kode_buku mengabaikan ID buku yang sedang di-edit
        $validated = $request->validate([
            'kode_buku' => 'required|string|unique:bukus,kode_buku,' . $id,
            'judul' => 'required|string|max:255',
            'jenis_buku_id' => 'required|exists:jenis_bukus,id',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'sinopsis' => 'required|string',
        ]);

        $buku->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data buku berhasil diperbarui di database pusat',
            'data' => $buku->load('jenisBuku')
        ], 200);
    }

    /**
     * FR-02 (Delete): Menghapus data buku dari database pusat (Khusus Petugas)
     */
    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data buku berhasil dihapus dari database pusat'
        ], 200);
    }
}