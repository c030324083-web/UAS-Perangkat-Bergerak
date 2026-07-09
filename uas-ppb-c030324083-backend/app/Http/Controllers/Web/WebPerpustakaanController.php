<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\JenisBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebPerpustakaanController extends Controller
{
    public function index(Request $request)
    {
        $jenisBukus = JenisBuku::all();
        $query = Buku::with('jenisBuku');

        // FR-05 Pencarian & Filter di Web
        if ($request->has('search') && $request->search != '') {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }
        if ($request->has('jenis_id') && $request->jenis_id != '') {
            $query->where('jenis_buku_id', $request->jenis_id);
        }

        $bukus = $query->latest()->get();

        return view('dashboard', compact('jenisBukus', 'bukus'));
    }

    public function store(Request $request)
    {
        // Proteksi Role di sisi backend Web (Hanya Petugas)
        if (!Auth::user()->hasRole('Petugas')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $validated = $request->validate([
            'kode_buku' => 'required|string|unique:bukus,kode_buku',
            'judul' => 'required|string|max:255',
            'jenis_buku_id' => 'required|exists:jenis_bukus,id',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'sinopsis' => 'required|string',
        ]);

        Buku::create($validated);

        return redirect()->back()->with('success', 'Buku baru berhasil diterbitkan di web!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // Menampilkan form login
    public function showLogin()
    {
        // Jika user sudah login, langsung lempar ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard.web');
        }
        return view('auth.login');
    }
    
    // Memproses autentikasi session web
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $remember = $request->has('remember');
    
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
    
            return redirect()->intended('dashboard')
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }
    
        return back()->withErrors([
            'email' => 'Kredensial yang dimasukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasRole('Petugas')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $buku = Buku::findOrFail($id);

        $validated = $request->validate([
            'kode_buku' => 'required|string|unique:bukus,kode_buku,' . $id,
            'judul' => 'required|string|max:255',
            'jenis_buku_id' => 'required|exists:jenis_bukus,id',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'sinopsis' => 'required|string',
        ]);

        $buku->update($validated);

        return redirect()->back()->with('success', 'Data buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->hasRole('Petugas')) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        $buku = Buku::findOrFail($id);
        $buku->delete();

        return redirect()->back()->with('success', 'Buku berhasil dihapus dari katalog!');
    }
}