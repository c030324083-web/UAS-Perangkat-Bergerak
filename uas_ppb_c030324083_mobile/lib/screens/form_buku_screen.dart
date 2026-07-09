import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../models/buku_model.dart';
import '../providers/buku_provider.dart';

class FormBukuScreen extends ConsumerStatefulWidget {
  final Buku? buku; // Null jika Tambah Baru, isi Objek jika mode Edit
  const FormBukuScreen({super.key, this.buku});

  @override
  ConsumerState<FormBukuScreen> createState() => _FormBukuScreenState();
}

class _FormBukuScreenState extends ConsumerState<FormBukuScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _kodeCtrl;
  late TextEditingController _judulCtrl;
  late TextEditingController _pengarangCtrl;
  late TextEditingController _penerbitCtrl;
  late TextEditingController _sinopsisCtrl;
  String? _selectedJenisId;

  @override
  void initState() {
    super.initState();
    _kodeCtrl = TextEditingController(text: widget.buku?.kodeBuku ?? '');
    _judulCtrl = TextEditingController(text: widget.buku?.judul ?? '');
    _pengarangCtrl = TextEditingController(text: widget.buku?.pengarang ?? '');
    _penerbitCtrl = TextEditingController(text: widget.buku?.penerbit ?? '');
    _sinopsisCtrl = TextEditingController(text: widget.buku?.sinopsis ?? '');
    if (widget.buku != null) {
      _selectedJenisId = widget.buku!.jenisBukuId.toString();
    }
  }

  @override
  Widget build(BuildContext context) {
    final jenisBukuState = ref.watch(jenisBukuProvider);
    final isEditMode = widget.buku != null;

    return Scaffold(
      appBar: AppBar(
        title: Text(isEditMode ? '📝 Edit Data Buku' : '📝 Tambah Buku Baru'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              TextFormField(
                controller: _kodeCtrl,
                decoration: const InputDecoration(labelText: 'Kode Buku', border: OutlineInputBorder()),
                validator: (val) => val!.isEmpty ? 'Wajib diisi' : null,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _judulCtrl,
                decoration: const InputDecoration(labelText: 'Judul Buku', border: OutlineInputBorder()),
                validator: (val) => val!.isEmpty ? 'Wajib diisi' : null,
              ),
              const SizedBox(height: 12),
              
              // Dropdown Jenis Buku Kategori
              jenisBukuState.when(
                data: (listJenis) => DropdownButtonFormField<String>(
                  value: _selectedJenisId,
                  decoration: const InputDecoration(labelText: 'Jenis Buku', border: OutlineInputBorder()),
                  items: listJenis.map((e) => DropdownMenuItem(value: e.id.toString(), child: Text(e.namaJenis))).toList(),
                  onChanged: (val) => _selectedJenisId = val,
                  validator: (val) => val == null ? 'Pilih Jenis Buku' : null,
                ),
                loading: () => const CircularProgressIndicator(),
                error: (_, __) => const Text('Gagal mengambil kategori'),
              ),
              const SizedBox(height: 12),
              
              TextFormField(
                controller: _pengarangCtrl,
                decoration: const InputDecoration(labelText: 'Pengarang', border: OutlineInputBorder()),
                validator: (val) => val!.isEmpty ? 'Wajib diisi' : null,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _penerbitCtrl,
                decoration: const InputDecoration(labelText: 'Penerbit', border: OutlineInputBorder()),
                validator: (val) => val!.isEmpty ? 'Wajib diisi' : null,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _sinopsisCtrl,
                maxLines: 4,
                decoration: const InputDecoration(labelText: 'Sinopsis', border: OutlineInputBorder()),
                validator: (val) => val!.isEmpty ? 'Wajib diisi' : null,
              ),
              const SizedBox(height: 24),
              
              // ==================== FR-03: KENDALI TOMBOL AKSI ====================
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(backgroundColor: Colors.blueAccent, foregroundColor: Colors.white),
                      onPressed: () async {
                        if (_formKey.currentState!.validate()) {
                          final bukuData = Buku(
                            id: widget.buku?.id ?? 0,
                            kodeBuku: _kodeCtrl.text,
                            judul: _judulCtrl.text,
                            jenisBukuId: int.parse(_selectedJenisId!),
                            pengarang: _pengarangCtrl.text,
                            penerbit: _penerbitCtrl.text,
                            sinopsis: _sinopsisCtrl.text,
                          );

                          bool success;
                          if (isEditMode) {
                            success = await ref.read(bukuProvider.notifier).updateBuku(widget.buku!.id, bukuData);
                          } else {
                            success = await ref.read(bukuProvider.notifier).tambahBuku(bukuData);
                          }

                          if (success) {
                            context.pop();
                          }
                        }
                      },
                      child: const Text('Kirim'),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () {
                        // Tombol Cancel: Kosongkan Form & Kembali (FR-03)
                        context.pop();
                      },
                      child: const Text('Cancel'),
                    ),
                  ),
                ],
              )
            ],
          ),
        ),
      ),
    );
  }
}