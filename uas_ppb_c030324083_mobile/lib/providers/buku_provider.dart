import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import '../models/buku_model.dart';
import 'auth_provider.dart'; 

const String baseUrl = "http://127.0.0.1:8000/api"; 

// Berikan tipe data eksplisit Provider<Dio> di sini untuk memutus cycle
final Provider<Dio> dioProvider = Provider<Dio>((ref) {
  final dio = Dio(BaseOptions(
    baseUrl: baseUrl,
    headers: {
      "Accept": "application/json",
      "Content-Type": "application/json",
    },
  ));

  dio.interceptors.add(InterceptorsWrapper(
    onRequest: (options, handler) {
      // PENTING: Ambil state terbaru TEPAT saat request dipicu, jangan di luar blok ini
      final token = ref.read(authProvider).token;
      
      if (token != null && token.isNotEmpty) {
        options.headers["Authorization"] = "Bearer $token";
      }
      
      return handler.next(options);
    },
    onError: (DioException e, handler) {
      print("🚨 API Error [${e.response?.statusCode}]: ${e.response?.data}");
      return handler.next(e);
    },
  ));

  return dio;
});

// 1. Ubah class Notifier agar menerima Ref lewat constructor
class BukuNotifier extends StateNotifier<AsyncValue<List<Buku>>> {
  final Dio dio;
  final Ref ref;

  BukuNotifier(this.dio, this.ref) : super(const AsyncValue.loading());

  Future<void> fetchBuku({String? search, String? jenisId}) async {
    try {
      // Pastikan state loading dipicu di awal
      state = const AsyncValue.loading();

      final Map<String, dynamic> queryParameters = {};
      if (search != null && search.isNotEmpty) queryParameters['search'] = search;
      if (jenisId != null && jenisId.isNotEmpty) queryParameters['jenis_buku_id'] = jenisId;

      final response = await dio.get('/buku', queryParameters: queryParameters);
      
      final List data = response.data['data'] ?? [];
      final listBuku = data.map((e) => Buku.fromJson(e)).toList();
      
      state = AsyncValue.data(listBuku); // Sukses: Berhenti loading, tampilkan data
    } catch (e, stack) {
      print("🚨 Gagal Fetch Buku: $e");
      state = AsyncValue.error(e, stack); // Error: Berhenti loading, tampilkan error widget
    }
  }

  Future<bool> tambahBuku(Buku buku) async {
    try {
      final currentToken = ref.read(authProvider).token;
      print("🔑 DEBUG TOKEN SEBELUM KIRIM (CREATE): $currentToken");

      final Map<String, dynamic> dataPayload = {
        'kode_buku': buku.kodeBuku,
        'judul': buku.judul,
        'jenis_buku_id': buku.jenisBukuId,
        'pengarang': buku.pengarang,
        'penerbit': buku.penerbit,
        'sinopsis': buku.sinopsis,
      };

      final response = await dio.post('/buku', data: dataPayload);
      
      // === PERBAIKAN: Jika sukses, panggil fetchBuku() agar katalog ter-refresh ===
      if (response.statusCode == 201 || response.statusCode == 200) {
        print("🍏 Tambah buku sukses, memperbarui katalog...");
        await fetchBuku();
        return true;
      }
      return false;
    } catch (e) {
      print("🚨 Error Tambah Buku: $e");
      return false;
    }
  }

  Future<bool> updateBuku(int id, Buku buku) async {
    try {
      final Map<String, dynamic> dataPayload = {
        '_method': 'PUT',
        'kode_buku': buku.kodeBuku,
        'judul': buku.judul,
        'jenis_buku_id': buku.jenisBukuId,
        'pengarang': buku.pengarang,
        'penerbit': buku.penerbit,
        'sinopsis': buku.sinopsis,
      };

      final response = await dio.post('/buku/$id', data: dataPayload);
      
      // === PERBAIKAN: Jika sukses, panggil fetchBuku() agar perubahan langsung muncul ===
      if (response.statusCode == 200) {
        print("🍏 Update buku sukses, memperbarui katalog...");
        await fetchBuku();
        return true;
      }
      return false;
    } catch (e) {
      print("🚨 Error Update Buku: $e");
      return false;
    }
  }

  Future<bool> deleteBuku(int id) async {
    try {
      await dio.delete('/buku/$id');
      fetchBuku(); 
      return true;
    } catch (e) {
      return false;
    }
  }
}

// 2. Di bagian deklarasi provider paling bawah, oper 'ref' ke dalam constructor-nya
final StateNotifierProvider<BukuNotifier, AsyncValue<List<Buku>>> bukuProvider =
    StateNotifierProvider<BukuNotifier, AsyncValue<List<Buku>>>((ref) {
  final dio = ref.watch(dioProvider);
  return BukuNotifier(dio, ref); // <--- Kirim objek ref ke sini
});

final FutureProvider<List<JenisBuku>> jenisBukuProvider = FutureProvider<List<JenisBuku>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get('/jenis-buku');
  final List data = response.data['data'];
  return data.map((e) => JenisBuku.fromJson(e)).toList();
});