class Buku {
  final int id;
  final String kodeBuku;
  final String judul;
  final int jenisBukuId;
  final String pengarang;
  final String penerbit;
  final String sinopsis;
  final String? namaJenis; // Relasi belongsTo ke jenis_buku

  Buku({
    required this.id,
    required this.kodeBuku,
    required this.judul,
    required this.jenisBukuId,
    required this.pengarang,
    required this.penerbit,
    required this.sinopsis,
    this.namaJenis,
  });

  factory Buku.fromJson(Map<String, dynamic> json) {
    return Buku(
      id: json['id'],
      kodeBuku: json['kode_buku'],
      judul: json['judul'],
      jenisBukuId: json['jenis_buku_id'],
      pengarang: json['pengarang'],
      penerbit: json['penerbit'],
      sinopsis: json['sinopsis'],
      namaJenis: json['jenis_buku'] != null ? json['jenis_buku']['nama_jenis'] : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'kode_buku': kodeBuku,
      'judul': judul,
      'jenis_buku_id': jenisBukuId,
      'pengarang': pengarang,
      'penerbit': penerbit,
      'sinopsis': sinopsis,
    };
  }
}

class JenisBuku {
  final int id;
  final String namaJenis;

  JenisBuku({required this.id, required this.namaJenis});

  factory JenisBuku.fromJson(Map<String, dynamic> json) {
    return JenisBuku(
      id: json['id'],
      namaJenis: json['nama_jenis'],
    );
  }
}