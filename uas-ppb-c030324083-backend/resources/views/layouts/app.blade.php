<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI Perpustakaan Poliban</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <nav class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <span class="font-bold text-xl tracking-wider">📚 PERPUSTAKAAN POLIBAN</span>
            <div class="flex items-center space-gap-4">
                <span class="bg-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                    {{ Auth::user()->name }} ({{ Auth::user()->role }})
                </span>
                <form action="{{ route('logout.web') }}" method="POST" class="inline ml-4">
                    @csrf
                    <button type="submit" class="text-sm bg-red-500 hover:bg-red-600 px-3 py-1 rounded transition">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8">
        @yield('content')
    </main>

</body>
</html>