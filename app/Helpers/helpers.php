<?php

if (!function_exists('formatStudentName')) {
    /**
     * Format nama siswa: maks 2 kata penuh, sisanya jadi inisial.
     * Kata yang sudah berupa inisial (misal "A.") tidak dihitung sebagai kata penuh.
     *
     * Contoh:
     * "Ridho Rio Kurniawan Abadi" → "Ridho Rio K. A."
     * "A. Supri Yanti Kurniawan" → "A. Supri Yanti K."
     * "Budi Santoso" → "Budi Santoso" (tidak berubah)
     */
    function formatStudentName(string $name, int $maxFullWords = 2): string
    {
        $words = array_filter(explode(' ', trim($name)));

        if (count($words) <= $maxFullWords) {
            return $name;
        }

        $result = [];
        $fullWordCount = 0;

        foreach ($words as $word) {
            // Deteksi apakah kata ini sudah berupa inisial: huruf tunggal opsional titik
            $isInitial = (bool) preg_match('/^[A-Za-z]\.?$/', $word);

            if ($isInitial) {
                // Normalisasi: pastikan pakai titik di belakang
                $result[] = rtrim($word, '.') . '.';
            } elseif ($fullWordCount < $maxFullWords) {
                $result[] = $word;
                $fullWordCount++;
            } else {
                // Jadikan inisial
                $result[] = strtoupper($word[0]) . '.';
            }
        }

        return implode(' ', $result);
    }
}
