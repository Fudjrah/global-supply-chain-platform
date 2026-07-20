<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Artikel Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-150 p-8">
                <form method="POST" action="{{ route('articles.store') }}">
                    @csrf

                    <!-- Title -->
                    <div>
                        <label for="title" class="block font-medium text-sm text-gray-700">Judul Artikel</label>
                        <input id="title" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="text" name="title" :value="old('title')" required autofocus />
                        @error('title')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description / Content -->
                    <div class="mt-4">
                        <label for="description" class="block font-medium text-sm text-gray-700">Isi Artikel / Deskripsi</label>
                        <textarea id="description" name="description" rows="5" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Source / Category -->
                    <div class="mt-4">
                        <label for="source" class="block font-medium text-sm text-gray-700">Kategori / Sumber</label>
                        <input id="source" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="text" name="source" :value="old('source')" placeholder="Contoh: Logistik, Cuaca, Hub Maritim" />
                        @error('source')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- URL -->
                    <div class="mt-4">
                        <label for="url" class="block font-medium text-sm text-gray-700">URL Referensi (Optional)</label>
                        <input id="url" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="url" name="url" :value="old('url')" placeholder="https://example.com/article" />
                        @error('url')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Published At -->
                    <div class="mt-4">
                        <label for="published_at" class="block font-medium text-sm text-gray-700">Tanggal Rilis</label>
                        <input id="published_at" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="date" name="published_at" value="{{ old('published_at', date('Y-m-d')) }}" required />
                        @error('published_at')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-8 gap-4">
                        <a class="text-sm text-gray-600 hover:text-gray-900" href="{{ route('articles.index') }}">
                            Batal
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
                            Simpan Artikel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
