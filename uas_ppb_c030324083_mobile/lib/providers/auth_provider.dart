import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'buku_provider.dart';

class AuthState {
  final bool isAuthenticated;
  final String? token;
  final String? role;
  final String? name;
  final String? errorMessage;

  AuthState({
    this.isAuthenticated = false,
    this.token,
    this.role,
    this.name,
    this.errorMessage,
  });

  AuthState copyWith({
    bool? isAuthenticated,
    String? token,
    String? role,
    String? name,
    String? errorMessage,
  }) {
    return AuthState(
      isAuthenticated: isAuthenticated ?? this.isAuthenticated,
      token: token ?? this.token,
      role: role ?? this.role,
      name: name ?? this.name,
      errorMessage: errorMessage,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final Dio dio;

  AuthNotifier(this.dio) : super(AuthState()) {
    _loadPersistedSession();
  }

  // PRD: Membaca session dengan proteksi nilai null agar tidak terjadi crash 'Null is not a subtype of String'
  Future<void> _loadPersistedSession() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final savedToken = prefs.getString('auth_token');
      final savedRole = prefs.getString('auth_role');
      final savedName = prefs.getString('auth_name');

      if (savedToken != null && savedToken.isNotEmpty) {
        state = AuthState(
          isAuthenticated: true,
          token: savedToken,
          role: savedRole ?? 'petugas', // Default fallback jika kosong
          name: savedName ?? 'User',
        );
      }
    } catch (e) {
      print("🚨 Gagal memuat session lokal: $e");
    }
  }

  Future<bool> login(String email, String password) async {
    try {
      final response = await dio.post('/login', data: {
        'email': email,
        'password': password,
      });

      if (response.statusCode == 200) {
        final data = response.data;
        print("🍏 DEBUG RESPONSE LOGIN RAW: $data"); // Cetak di konsol biar kita tahu isi pastinya

        // LOGIK EKSTRAKSI SUPER AMAN: Cek semua kemungkinan key token dari Laravel
        final String? token = data['token'] ?? 
                              data['access_token'] ?? 
                              data['data']?['token'] ?? 
                              data['data']?['access_token'];

        // Cek semua kemungkinan key user
        final dynamic userData = data['user'] ?? data['data']?['user'] ?? data['data'];

        if (token != null) {
          final prefs = await SharedPreferences.getInstance();
          await prefs.setString('auth_token', token);
          
          // Pastikan role dan name aman dari null string
          String userRole = 'petugas';
          String userName = 'Petugas Poliban';

          if (userData != null && userData is Map) {
            userRole = userData['role']?.toString() ?? 'petugas';
            userName = userData['name']?.toString() ?? 'Petugas Poliban';
          }

          await prefs.setString('auth_role', userRole);
          await prefs.setString('auth_name', userName);

          state = AuthState(
            isAuthenticated: true,
            token: token,
            role: userRole,
            name: userName,
          );
          return true;
        } else {
          print("🚨 ERROR: Token tidak ditemukan di dalam response JSON Laravel!");
        }
      }
      return false;
    } catch (e) {
      print("🚨 EROR DETAIL LOGIN CATCH: $e");
      
      String msg = "Terjadi kesalahan sistem.";
      if (e is DioException && e.response != null) {
        msg = e.response?.data['message'] ?? "Email atau password salah.";
      }
      state = state.copyWith(errorMessage: msg);
      return false;
    }
  }

  Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    await prefs.remove('auth_role');
    await prefs.remove('auth_name');
    state = AuthState(); 
  }
}

final StateNotifierProvider<AuthNotifier, AuthState> authProvider = 
    StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  final dio = ref.read(dioProvider);
  return AuthNotifier(dio);
});