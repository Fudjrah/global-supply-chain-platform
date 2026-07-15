<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Anda</title>
    <!-- Masukkan CSS Anda di sini -->
</head>
<body>

    <!-- Header dengan Dropdown (Ini yang tadi) -->
    <header style="padding: 15px; border-bottom: 1px solid #ccc;">
        <nav>
            <form action="{{ route('search') }}" method="GET">
                <select name="iso_code" onchange="this.form.submit()">
                    <option value="">Pilih Negara...</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->iso_code }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </form>
        </nav>
    </header>

    <!-- Content akan berubah sesuai halaman (Dashboard atau Hasil Pencarian) -->
    <main>
        @yield('content')
    </main>

</body>
</html>