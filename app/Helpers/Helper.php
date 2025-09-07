<?php

/**
 * Dokumentasi untuk Fungsi Pembantu
 *
 * File ini berisi berbagai fungsi pembantu yang dapat digunakan di seluruh aplikasi.
 *
 * 1. formatDate($date, $format = 'd F Y')
 *    - Memformat tanggal ke format yang ditentukan menggunakan Carbon.
 *    - Format default adalah 'd F Y'.
 *    - Penggunaan:
 *      $formattedDate = formatDate('2025-01-01'); // Mengembalikan '01 Januari 2025'
 *
 * 2. uploadFile($file, $folder, $disk = 'public')
 *    - Mengunggah file ke folder yang ditentukan.
 *    - Penggunaan:
 *      $filePath = uploadFile($request->file('document'), 'uploads');
 *
 * 3. deleteFile($path)
 *    - Menghapus file berdasarkan jalur yang diberikan.
 *    - Penggunaan:
 *      deleteFile('uploads/image.jpg');
 *
 * 4. indonesianCurrency($number)
 *    - Memformat angka ke format mata uang Indonesia.
 *    - Penggunaan:
 *      $formatted = indonesianCurrency(1000000); // Mengembalikan 'Rp 1.000.000'
 *
 *  ©️ 2025 by kenndeclouv
 *  https://kenndeclouv.my.id
 */

use Illuminate\Support\Facades\Storage;

if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'd F Y')
    {
        return \Carbon\Carbon::parse($date)->translatedFormat($format);
    }
}

if (!function_exists('uploadFile')) {
    function uploadFile($file, $folder, $disk = 'public')
    {
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $file->getClientOriginalName());
        $file->storeAs($folder, $filename, $disk);
        return $filename;
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
if (!function_exists('indonesianCurrency')) {
    function indonesianCurrency($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
}

if (!function_exists('convertCase')) {
    function convertCase(string $input, string $targetCase): string
    {
        if (!preg_match('/[_\s-]|[a-z][A-Z]/', $input)) {
            throw new InvalidArgumentException("Input must have clear word boundaries (e.g., snake_case or camelCase).");
        }

        $normalized = preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $input); // camelCase -> camel Case
        $normalized = strtolower(preg_replace('/[_-]/', ' ', $normalized)); // snake_case/kebab-case -> snake case

        switch ($targetCase) {
            case 'camel':
                return lcfirst(str_replace(' ', '', ucwords($normalized)));
            case 'pascal':
                return str_replace(' ', '', ucwords($normalized));
            case 'snake':
                return strtolower(str_replace(' ', '_', $normalized));
            case 'kebab':
                return strtolower(str_replace(' ', '-', $normalized));
            case 'lowercase':
                return strtolower($normalized);
            case 'uppercase':
                return strtoupper($normalized);
            default:
                throw new InvalidArgumentException("Invalid target case: $targetCase");
        }
    }
}

if (!function_exists('getProvinsi')) {
    function getProvinsi()
    {
        $path = public_path('data-indonesia/provinsi.json');
        if (!file_exists($path)) {
            return [];
        }
        $json = file_get_contents($path);
        return json_decode($json, true) ?? [];
    }
}

if (!function_exists('getKabupaten')) {
    function getKabupaten($provinsiId)
    {
        // Variabel statis akan 'mengingat' nilainya selama request berjalan
        static $cache = [];

        if (isset($cache[$provinsiId])) {
            return $cache[$provinsiId]; // Jika sudah ada di cache, langsung kembalikan
        }

        $path = public_path("data-indonesia/kabupaten/{$provinsiId}.json");
        if (!file_exists($path)) {
            return [];
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true) ?? [];

        // Simpan hasilnya ke cache sebelum dikembalikan
        $cache[$provinsiId] = $data;

        return $data;
    }
}

// Lakukan hal yang sama untuk Kecamatan dan Kelurahan
if (!function_exists('getKecamatan')) {
    function getKecamatan($kabupatenId)
    {
        static $cache = [];
        if (isset($cache[$kabupatenId])) return $cache[$kabupatenId];

        $path = public_path("data-indonesia/kecamatan/{$kabupatenId}.json");
        if (!file_exists($path)) return [];

        $json = file_get_contents($path);
        $data = json_decode($json, true) ?? [];
        $cache[$kabupatenId] = $data;
        return $data;
    }
}

if (!function_exists('getKelurahan')) {
    function getKelurahan($kecamatanId)
    {
        static $cache = [];
        if (isset($cache[$kecamatanId])) return $cache[$kecamatanId];

        $path = public_path("data-indonesia/kecamatan/{$kecamatanId}.json");
        if (!file_exists($path)) return [];

        $json = file_get_contents($path);
        $data = json_decode($json, true) ?? [];
        $cache[$kecamatanId] = $data;
        return $data;
    }
}
