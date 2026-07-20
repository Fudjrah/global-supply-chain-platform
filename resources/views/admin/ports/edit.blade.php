<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Pelabuhan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-150 p-8">
                <form method="POST" action="{{ route('ports.update', $port) }}">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block font-medium text-sm text-gray-700">Nama Pelabuhan</label>
                        <input id="name" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="text" name="name" value="{{ old('name', $port->name) }}" required autofocus />
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div class="mt-4">
                        <label for="country_id" class="block font-medium text-sm text-gray-700">Negara</label>
                        <select id="country_id" name="country_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">Pilih Negara...</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id', $port->country_id) == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }} ({{ $country->country_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('country_id')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Latitude -->
                    <div class="mt-4">
                        <label for="latitude" class="block font-medium text-sm text-gray-700">Latitude</label>
                        <input id="latitude" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="number" step="0.00000001" name="latitude" value="{{ old('latitude', $port->latitude) }}" required />
                        @error('latitude')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Longitude -->
                    <div class="mt-4">
                        <label for="longitude" class="block font-medium text-sm text-gray-700">Longitude</label>
                        <input id="longitude" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="number" step="0.00000001" name="longitude" value="{{ old('longitude', $port->longitude) }}" required />
                        @error('longitude')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Port Type -->
                    <div class="mt-4">
                        <label for="type" class="block font-medium text-sm text-gray-700">Tipe Pelabuhan</label>
                        <input id="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="text" name="type" placeholder="Contoh: Seaport, Container Terminal" value="{{ old('type', $port->type) }}" />
                        @error('type')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-8 gap-4">
                        <a class="text-sm text-gray-600 hover:text-gray-900" href="{{ route('ports.index') }}">
                            Batal
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
