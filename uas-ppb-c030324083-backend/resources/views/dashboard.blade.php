@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 {{ Auth::user()->hasRole('Petugas') ? 'lg:grid-cols-3' : 'grid-cols-1' }} gap-8" 
     x-data="{ 
        isEdit: false, 
        actionUrl: '{{ route('buku.store.web') }}',
        bukuId: '',
        kodeBuku: '',
        judul: '',
        jenisId: '',
        pengarang: '',
        penerbit: '',
        sinopsis: '',
        openSinopsis: null,
        
        startEdit(buku) {
            this.isEdit = true;
            this.bukuId = buku.id;
            this.actionUrl = '/buku/' + buku.id;
            this.kodeBuku = buku.kode_buku;
            this.judul = buku.judul;
            this.jenisId = buku.jenis_buku_id;
            this.pengarang = buku.pengarang;
            this.penerbit = buku.penerbit;
            this.sinopsis = buku.sinopsis;
        },
        cancelEdit() {
            this.isEdit = false;
            this.actionUrl = '{{ route('buku.store.web') }}';
            this.bukuId = '';
            this.kodeBuku = '';
            this.judul = '';
            this.jenisId = '';
            this.pengarang = '';
            this.penerbit = '';
            this.sinopsis = '';
        }
     }">
    
    @if(Auth::user()->hasRole('Petugas'))
    <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-md h-fit">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2" x-text="isEdit ? '📝 Edit Data Buku' : '📝 Tambah Buku Baru'"></h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form :action="actionUrl" method="POST" class="space-y-4">
            @csrf
            <template x-if="isEdit">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Buku</label>
                <input type="text" name="kode_buku" x-model="kodeBuku" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: BK-001" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku</label>
                <input type="text" name="judul" x-model="judul" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Masukkan judul" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis / Kategori Buku</label>
                <select name="jenis_buku_id" x-model="jenisId" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    <option value="">-- Pilih Jenis --</option>
                    @foreach($jenisBukus as $jenis)
                        <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengarang</label>
                    <input type="text" name="pengarang" x-model="pengarang" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                    <input type="text" name="penerbit" x-model="penerbit" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sinopsis</label>
                <textarea name="sinopsis" x-model="sinopsis" rows="4" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Tulis sinopsis cerita buku..." required></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition" x-text="isEdit ? 'Update Pusat' : 'Simpan Pusat'"></button>
                <button type="button" @click="cancelEdit()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition">Cancel</button>
            </div>
        </form>
    </div>
    @endif

    <div class="{{ Auth::user()->hasRole('Petugas') ? 'lg:col-span-2' : 'col-span-1' }} bg-white p-6 rounded-xl shadow-md">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-800">📚 Katalog Buku Perpustakaan</h2>
            
            <form action="{{ route('dashboard.web') }}" method="GET" class="flex flex-wrap gap-2 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku..." class="px-3 py-1.5 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-48">
                
                <select name="jenis_id" class="px-3 py-1.5 border rounded-lg text-sm outline-none bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($jenisBukus as $jenis)
                        <option value="{{ $jenis->id }}" {{ request('jenis_id') == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama_jenis }}</option>
                    @endforeach
                </select>
                
                <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-gray-900 transition">Cari</button>
                @if(request('search') || request('jenis_id'))
                    <a href="{{ route('dashboard.web') }}" class="bg-gray-200 text-gray-700 text-sm px-3 py-1.5 rounded-lg hover:bg-gray-300 transition flex items-center">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b text-gray-700 text-sm font-semibold">
                        <th class="p-3">Kode</th>
                        <th class="p-3">Judul Buku</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Pengarang / Penerbit</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-600 divide-y">
                    @forelse($bukus as $buku)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 font-mono font-bold text-blue-600">{{ $buku->kode_buku }}</td>
                        <td class="p-3 font-semibold text-gray-900">{{ $buku->judul }}</td>
                        <td class="p-3">
                            <span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded text-xs font-medium border">
                                {{ $buku->jenisBuku->nama_jenis }}
                            </span>
                        </td>
                        <td class="p-3">
                            <span class="block font-medium text-gray-800">{{ $buku->pengarang }}</span>
                            <span class="block text-xs text-gray-400">Penerbit: {{ $buku->penerbit }}</span>
                        </td>
                        <td class="p-3 text-center space-y-1">
                            <button @click="openSinopsis = (openSinopsis === {{ $buku->id }} ? null : {{ $buku->id }})" class="text-blue-600 hover:text-blue-800 font-semibold underline text-xs block mx-auto">
                                <span x-text="openSinopsis === {{ $buku->id }} ? 'Tutup' : 'Baca Sinopsis'"></span>
                            </button>
                            
                            @if(Auth::user()->hasRole('Petugas'))
                            <div class="flex justify-center gap-3 pt-1 border-t border-dashed mt-1">
                                <button @click="startEdit({{ json_encode($buku) }})" class="text-amber-600 hover:text-amber-800 text-xs font-bold transition">Edit</button>
                                
                                <form action="{{ route('buku.destroy.web', $buku->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus buku ini dari database pusat?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold transition">Hapus</button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    <tr x-show="openSinopsis === {{ $buku->id }}" x-cloak class="bg-blue-50/50">
                        <td colspan="5" class="p-4 border-t text-xs md:text-sm text-gray-700 leading-relaxed">
                            <div class="p-3 bg-white border rounded-lg shadow-sm">
                                <strong class="block text-gray-900 mb-1">Sinopsis Lengkap:</strong>
                                {{ $buku->sinopsis }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">Belum ada data buku yang sesuai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection