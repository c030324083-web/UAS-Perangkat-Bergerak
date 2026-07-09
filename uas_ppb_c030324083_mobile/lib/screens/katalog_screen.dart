import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../providers/buku_provider.dart';
import '../providers/auth_provider.dart'; // <--- PASTIKAN IMPORT INI ADA SEKARANG CIK!
import 'dart:math';

class KatalogScreen extends ConsumerStatefulWidget {
  const KatalogScreen({super.key});

  @override
  ConsumerState<KatalogScreen> createState() => _KatalogScreenState();
}

class _KatalogScreenState extends ConsumerState<KatalogScreen> {
  final TextEditingController _searchCtrl = TextEditingController();
  String? _selectedJenisId;

  @override
  void initState() {
    super.initState();
    // Gunakan Future.microtask agar aman memicu provider saat render lifecycle Flutter berjalan
    Future.microtask(() {
      // Ambil data buku pertama kali ke backend Laravel
      ref.read(bukuProvider.notifier).fetchBuku();
    });
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final bukuState = ref.watch(bukuProvider);
    final jenisBukuState = ref.watch(jenisBukuProvider);
    
    // Membaca real-time auth role hasil login dari database pusat
    final authState = ref.watch(authProvider);
    final currentRole = authState.role ?? 'Anggota';

    return Scaffold(
      appBar: AppBar(
        title: const Text('📚 Katalog Poliban', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.blueAccent,
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            tooltip: 'Logout',
            onPressed: () {
              ref.read(authProvider.notifier).logout();
              context.go('/login');
            },
          )
        ],
      ),
      body: Column(
        children: [
          // Pencarian & Filter Kategori
          Padding(
            padding: const EdgeInsets.all(12.0),
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _searchCtrl,
                    decoration: const InputDecoration(
                      labelText: 'Cari Judul Buku...',
                      prefixIcon: Icon(Icons.search),
                      border: OutlineInputBorder(),
                      contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                jenisBukuState.when(
                  data: (listJenis) => DropdownButton<String>(
                    value: _selectedJenisId,
                    hint: const Text('Kategori'),
                    items: [
                      const DropdownMenuItem(value: '', child: Text('Semua')),
                      ...listJenis.map((e) => DropdownMenuItem(
                            value: e.id.toString(),
                            child: Text(e.namaJenis),
                          ))
                    ],
                    onChanged: (val) => setState(() => _selectedJenisId = val),
                  ),
                  loading: () => const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(strokeWidth: 2),
                  ),
                  error: (_, __) => const Icon(Icons.error, color: Colors.red),
                ),
                const SizedBox(width: 8),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.blueAccent,
                    foregroundColor: Colors.white,
                  ),
                  onPressed: () {
                    ref.read(bukuProvider.notifier).fetchBuku(
                          search: _searchCtrl.text,
                          jenisId: _selectedJenisId ?? '',
                        );
                  },
                  child: const Text('Cari'),
                )
              ],
            ),
          ),

          // Daftar Katalog Buku
          Expanded(
            child: bukuState.when(
              data: (listBuku) => listBuku.isEmpty 
                ? const Center(child: Text('Belum ada data buku yang sesuai.'))
                : ListView.builder(
                itemCount: listBuku.length,
                itemBuilder: (context, index) {
                  final buku = listBuku[index];
                  return Card(
                    margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    child: ExpansionTile(
                      leading: CircleAvatar(
                        backgroundColor: Colors.blue.shade50,
                        child: Text(
                          buku.kodeBuku.substring(0, min(2, buku.kodeBuku.length)), 
                          style: const TextStyle(fontWeight: FontWeight.bold)
                        ),
                      ),
                      title: Text(buku.judul, style: const TextStyle(fontWeight: FontWeight.bold)),
                      subtitle: Text('${buku.pengarang} | Kategori: ${buku.namaJenis ?? '-'}'),
                      children: [
                        Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Align(
                            alignment: Alignment.centerLeft,
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('Penerbit: ${buku.penerbit}', style: const TextStyle(color: Colors.grey)),
                                const SizedBox(height: 8),
                                const Text('Sinopsis:', style: TextStyle(fontWeight: FontWeight.bold)),
                                const SizedBox(height: 4),
                                Text(buku.sinopsis, style: const TextStyle(height: 1.4, color: Colors.black87)),
                                
                                // Opsi Manajemen Khusus Petugas (CRUD) -> Memakai currentRole dinamis
                                if (currentRole == "Petugas") ...[
                                  const Divider(height: 24),
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.end,
                                    children: [
                                      TextButton.icon(
                                        icon: const Icon(Icons.edit, color: Colors.orange),
                                        label: const Text('Edit', style: TextStyle(color: Colors.orange)),
                                        onPressed: () => context.push('/form', extra: buku),
                                      ),
                                      const SizedBox(width: 8),
                                      TextButton.icon(
                                        icon: const Icon(Icons.delete, color: Colors.red),
                                        label: const Text('Hapus', style: TextStyle(color: Colors.red)),
                                        onPressed: () {
                                          showDialog(
                                            context: context,
                                            builder: (ctx) => AlertDialog(
                                              title: const Text('Hapus Buku'),
                                              content: const Text('Yakin ingin menghapus data buku ini dari database pusat?'),
                                              actions: [
                                                TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
                                                TextButton(
                                                  onPressed: () {
                                                    ref.read(bukuProvider.notifier).deleteBuku(buku.id);
                                                    Navigator.pop(ctx);
                                                  },
                                                  child: const Text('Hapus', style: TextStyle(color: Colors.red)),
                                                ),
                                              ],
                                            ),
                                          );
                                        },
                                      ),
                                    ],
                                  )
                                ]
                              ],
                            ),
                          ),
                        )
                      ],
                    ),
                  );
                },
              ),
              loading: () => const Center(child: CircularProgressIndicator()),
              error: (err, _) => Center(child: Text('Gagal memuat data: $err')),
            ),
          ),
        ],
      ),
      // Tombol Tambah Buku Baru Hanya Terbuka Untuk Petugas
      floatingActionButton: currentRole == "Petugas" 
          ? FloatingActionButton(
              backgroundColor: Colors.blueAccent,
              foregroundColor: Colors.white,
              onPressed: () => context.push('/form'),
              child: const Icon(Icons.add),
            )
          : null,
    );
  }
}