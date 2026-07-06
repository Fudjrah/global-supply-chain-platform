<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Risk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md p-8 bg-gray-800 rounded-2xl shadow-2xl border border-gray-700">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-400">Supply Chain Tracker</h1>
            <p class="text-gray-400 mt-2">Cek risiko logistik & ekonomi real-time</p>
        </div>

        <form action="/track" method="GET" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nama Negara</label>
                <input type="text" name="name" required placeholder="Contoh: Thailand" 
                       class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none transition">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Kode Negara (ISO)</label>
                <input type="text" name="code" required placeholder="Contoh: TH" 
                       class="w-full p-3 rounded-lg bg-gray-700 border border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none transition uppercase">
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-lg transition duration-200 mt-4 shadow-lg">
                Cek Risiko Sekarang
            </button>
        </form>
    </div>

</body>
</html>