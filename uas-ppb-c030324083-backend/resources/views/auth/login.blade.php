<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SI Perpustakaan Poliban</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-600 to-indigo-900 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition hover:scale-[1.01]">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">POLIBAN</h1>
            <p class="text-sm text-gray-500 mt-1">Sistem Informasi Perpustakaan Pusat</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs md:text-sm text-red-700 font-medium">
                            {{ $errors->first() }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('login.web.post') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full pl-3 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-800 placeholder-gray-400" 
                        placeholder="nama@poliban.ac.id" required autocomplete="email" autofocus>
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-sm font-semibold text-gray-700">Password</label>
                </div>
                <div class="relative">
                    <input type="password" name="password" 
                        class="w-full pl-3 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm text-gray-800 placeholder-gray-400" 
                        placeholder="••••••••" required autocomplete="current-password">
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">Ingat saya</label>
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition duration-150 ease-in-out transform active:scale-[0.98]">
                Masuk ke Sistem
            </button>
        </form>
    </div>

</body>
</html>