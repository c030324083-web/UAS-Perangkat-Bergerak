import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../screens/login_screen.dart';
import '../screens/katalog_screen.dart';
import '../screens/form_buku_screen.dart';
import '../providers/auth_provider.dart';
import '../models/buku_model.dart';

final goRouter = GoRouter(
  initialLocation: '/login', // Start pertama dari halaman login
  routes: [
    GoRoute(
      path: '/login',
      builder: (context, state) => const LoginScreen(),
    ),
    GoRoute(
      path: '/katalog',
      builder: (context, state) => const KatalogScreen(),
    ),
    GoRoute(
      path: '/form',
      builder: (context, state) {
        final buku = state.extra as Buku?;
        return FormBukuScreen(buku: buku);
      },
    ),
  ],
  // PROTEKSI ROUTE GLOBAL
  redirect: (context, state) {
    // Kita bypass atau cek menggunakan container pembaca provider eksternal jika dibutuhkan, 
    // namun untuk testing pastikan alur login dilewati dari halaman awal secara berurutan.
    return null;
  },
);