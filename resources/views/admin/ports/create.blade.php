<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelabuhan | Admin PortRisk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex min-h-screen">

    @include('admin.partials.sidebar', ['active' => 'ports'])

    <main class="flex-1 p-8 overflow-auto">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('ports.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Kembali ke Daftar Pelabuhan</a>
                <h1 class="text-2xl font-extrabold text-gray-900 mt-2">Tambah Pelabuhan Baru</h1>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <form method="POST" action="{{ route('ports.store') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Pelabuhan</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-5">
                        <label for="country_id" class="block text-sm font-semibold text-gray-700 mb-1">Negara</label>
                        <select id="country_id" name="country_id" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                            <option value="">Pilih Negara...</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }} ({{ $country->country_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('country_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-1">Latitude</label>
                            <input id="latitude" type="number" step="0.00000001" name="latitude" value="{{ old('latitude') }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                            @error('latitude') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-1">Longitude</label>
                            <input id="longitude" type="number" step="0.00000001" name="longitude" value="{{ old('longitude') }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                            @error('longitude') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="type" class="block text-sm font-semibold text-gray-700 mb-1">Tipe Pelabuhan</label>
                        <input id="type" type="text" name="type" value="{{ old('type') }}" placeholder="Contoh: Seaport, Container Terminal"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        @error('type') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('ports.index') }}" class="text-sm text-gray-500 hover:text-gray-800">Batal</a>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl shadow transition text-sm">
                            Simpan Pelabuhan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
