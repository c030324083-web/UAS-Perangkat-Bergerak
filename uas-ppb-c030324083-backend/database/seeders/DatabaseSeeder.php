<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\JenisBuku;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role (Spatie)
        $rolePetugas = Role::create(['name' => 'Petugas']);
        $roleAnggota = Role::create(['name' => 'Anggota']);

        // 2. Buat Akun Dummy
        $petugas = User::create([
            'name' => 'Dzaki Admin',
            'email' => 'petugas@poliban.ac.id',
            'password' => bcrypt('password123'),
        ]);
        $petugas->assignRole($rolePetugas);

        $anggota = User::create([
            'name' => 'Budi Mahasiswa',
            'email' => 'anggota@poliban.ac.id',
            'password' => bcrypt('password123'),
        ]);
        $anggota->assignRole($roleAnggota);

        // 3. Buat Kategori/Jenis Buku
        JenisBuku::create(['nama_jenis' => 'Java']);
        JenisBuku::create(['nama_jenis' => 'PHP']);
        JenisBuku::create(['nama_jenis' => 'Mobile Dev']);
    }
}