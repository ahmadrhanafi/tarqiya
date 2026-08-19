<?php

namespace App\Models;

use CodeIgniter\Model;

class HafalanModel extends Model
{
    protected $table = 'hafalan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    // Sesuaikan dengan kolom yang ada di database migration
    protected $allowedFields = [
        'tenant_id',
        'id_santri',
        'id_guru',
        'jenis',
        'juz',
        'surah',
        'ayat_mulai',
        'ayat_selesai',
        'predikat',
        'keterangan'
    ];

    // Mengaktifkan fitur timestamp otomatis (created_at & updated_at)
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // ==========================================
    // MASTER DATA JUZ DAN SURAH
    // ==========================================
// Endpoint AJAX untuk mengambil data surah berdasarkan Juz
    public function getSurahByJuz($juz)
    {
        $mappingJuz = [
            1 => [
                ['nama' => 'Al-Fatihah', 'min' => 1, 'max' => 7],
                ['nama' => 'Al-Baqarah', 'min' => 1, 'max' => 141]
            ],
            2 => [
                ['nama' => 'Al-Baqarah', 'min' => 142, 'max' => 252]
            ],
            3 => [
                ['nama' => 'Al-Baqarah', 'min' => 253, 'max' => 286],
                ['nama' => 'Ali \'Imran', 'min' => 1, 'max' => 92]
            ],
            4 => [
                ['nama' => 'Ali \'Imran', 'min' => 93, 'max' => 200],
                ['nama' => 'An-Nisa\'', 'min' => 1, 'max' => 23]
            ],
            5 => [
                ['nama' => 'An-Nisa\'', 'min' => 24, 'max' => 147]
            ],
            6 => [
                ['nama' => 'An-Nisa\'', 'min' => 148, 'max' => 176],
                ['nama' => 'Al-Ma\'idah', 'min' => 1, 'max' => 81]
            ],
            7 => [
                ['nama' => 'Al-Ma\'idah', 'min' => 82, 'max' => 120],
                ['nama' => 'Al-An\'am', 'min' => 1, 'max' => 110]
            ],
            8 => [
                ['nama' => 'Al-An\'am', 'min' => 111, 'max' => 165],
                ['nama' => 'Al-A\'raf', 'min' => 1, 'max' => 87]
            ],
            9 => [
                ['nama' => 'Al-A\'raf', 'min' => 88, 'max' => 206],
                ['nama' => 'Al-Anfal', 'min' => 1, 'max' => 40]
            ],
            10 => [
                ['nama' => 'Al-Anfal', 'min' => 41, 'max' => 75],
                ['nama' => 'At-Taubah', 'min' => 1, 'max' => 92]
            ],
            11 => [
                ['nama' => 'At-Taubah', 'min' => 93, 'max' => 129],
                ['nama' => 'Yunus', 'min' => 1, 'max' => 109],
                ['nama' => 'Hud', 'min' => 1, 'max' => 5]
            ],
            12 => [
                ['nama' => 'Hud', 'min' => 6, 'max' => 123],
                ['nama' => 'Yusuf', 'min' => 1, 'max' => 52]
            ],
            13 => [
                ['nama' => 'Yusuf', 'min' => 53, 'max' => 111],
                ['nama' => 'Ar-Ra\'d', 'min' => 1, 'max' => 43],
                ['nama' => 'Ibrahim', 'min' => 1, 'max' => 52]
            ],
            14 => [
                ['nama' => 'Al-Hijr', 'min' => 1, 'max' => 99],
                ['nama' => 'An-Nahl', 'min' => 1, 'max' => 128]
            ],
            15 => [
                ['nama' => 'Al-Isra\'', 'min' => 1, 'max' => 111],
                ['nama' => 'Al-Kahf', 'min' => 1, 'max' => 74]
            ],
            16 => [
                ['nama' => 'Al-Kahf', 'min' => 75, 'max' => 110],
                ['nama' => 'Maryam', 'min' => 1, 'max' => 98],
                ['nama' => 'Taha', 'min' => 1, 'max' => 135]
            ],
            17 => [
                ['nama' => 'Al-Anbiya\'', 'min' => 1, 'max' => 112],
                ['nama' => 'Al-Hajj', 'min' => 1, 'max' => 78]
            ],
            18 => [
                ['nama' => 'Al-Mu\'minun', 'min' => 1, 'max' => 118],
                ['nama' => 'An-Nur', 'min' => 1, 'max' => 64],
                ['nama' => 'Al-Furqan', 'min' => 1, 'max' => 20]
            ],
            19 => [
                ['nama' => 'Al-Furqan', 'min' => 21, 'max' => 77],
                ['nama' => 'Ash-Shu\'ara\'', 'min' => 1, 'max' => 227],
                ['nama' => 'An-Naml', 'min' => 1, 'max' => 55]
            ],
            20 => [
                ['nama' => 'An-Naml', 'min' => 56, 'max' => 93],
                ['nama' => 'Al-Qasas', 'min' => 1, 'max' => 88],
                ['nama' => 'Al-Ankabut', 'min' => 1, 'max' => 45]
            ],
            21 => [
                ['nama' => 'Al-Ankabut', 'min' => 46, 'max' => 69],
                ['nama' => 'Ar-Rum', 'min' => 1, 'max' => 60],
                ['nama' => 'Luqman', 'min' => 1, 'max' => 34],
                ['nama' => 'As-Sajdah', 'min' => 1, 'max' => 30],
                ['nama' => 'Al-Ahzab', 'min' => 1, 'max' => 30]
            ],
            22 => [
                ['nama' => 'Al-Ahzab', 'min' => 31, 'max' => 73],
                ['nama' => 'Saba\'', 'min' => 1, 'max' => 54],
                ['nama' => 'Fatir', 'min' => 1, 'max' => 45],
                ['nama' => 'Ya-Sin', 'min' => 1, 'max' => 27]
            ],
            23 => [
                ['nama' => 'Ya-Sin', 'min' => 28, 'max' => 83],
                ['nama' => 'As-Saffat', 'min' => 1, 'max' => 182],
                ['nama' => 'Sad', 'min' => 1, 'max' => 88]
            ],
            24 => [
                ['nama' => 'Az-Zumar', 'min' => 1, 'max' => 75],
                ['nama' => 'Gafir', 'min' => 1, 'max' => 85],
                ['nama' => 'Fussilat', 'min' => 1, 'max' => 46]
            ],
            25 => [
                ['nama' => 'Fussilat', 'min' => 47, 'max' => 54],
                ['nama' => 'Ash-Shura', 'min' => 1, 'max' => 53],
                ['nama' => 'Az-Zukhruf', 'min' => 1, 'max' => 89],
                ['nama' => 'Ad-Dukhan', 'min' => 1, 'max' => 59],
                ['nama' => 'Al-Jasiyah', 'min' => 1, 'max' => 37]
            ],
            26 => [
                ['nama' => 'Al-Ahqaf', 'min' => 1, 'max' => 35],
                ['nama' => 'Muhammad', 'min' => 1, 'max' => 38],
                ['nama' => 'Al-Fath', 'min' => 1, 'max' => 29],
                ['nama' => 'Al-Hujurat', 'min' => 1, 'max' => 18],
                ['nama' => 'Qaf', 'min' => 1, 'max' => 45],
                ['nama' => 'Ad-Zariyat', 'min' => 1, 'max' => 30]
            ],
            27 => [
                ['nama' => 'Ad-Zariyat', 'min' => 31, 'max' => 60],
                ['nama' => 'At-Tur', 'min' => 1, 'max' => 49],
                ['nama' => 'An-Najm', 'min' => 1, 'max' => 62],
                ['nama' => 'Al-Qamar', 'min' => 1, 'max' => 55],
                ['nama' => 'Ar-Rahman', 'min' => 1, 'max' => 78],
                ['nama' => 'Al-Waqi\'ah', 'min' => 1, 'max' => 96],
                ['nama' => 'Al-Hadid', 'min' => 1, 'max' => 29]
            ],
            28 => [
                ['nama' => 'Al-Mujadilah', 'min' => 1, 'max' => 22],
                ['nama' => 'Al-Hasyr', 'min' => 1, 'max' => 24],
                ['nama' => 'Al-Mumtahanah', 'min' => 1, 'max' => 13],
                ['nama' => 'As-Saff', 'min' => 1, 'max' => 14],
                ['nama' => 'Al-Jumu\'ah', 'min' => 1, 'max' => 11],
                ['nama' => 'Al-Munafiqun', 'min' => 1, 'max' => 11],
                ['nama' => 'At-Tagabun', 'min' => 1, 'max' => 18],
                ['nama' => 'At-Talaq', 'min' => 1, 'max' => 12],
                ['nama' => 'At-Tahrim', 'min' => 1, 'max' => 12]
            ],
            29 => [
                ['nama' => 'Al-Mulk', 'min' => 1, 'max' => 30],
                ['nama' => 'Al-Qalam', 'min' => 1, 'max' => 52],
                ['nama' => 'Al-Haqqah', 'min' => 1, 'max' => 52],
                ['nama' => 'Al-Ma\'arij', 'min' => 1, 'max' => 44],
                ['nama' => 'Nuh', 'min' => 1, 'max' => 28],
                ['nama' => 'Al-Jinn', 'min' => 1, 'max' => 28],
                ['nama' => 'Al-Muzzammil', 'min' => 1, 'max' => 20],
                ['nama' => 'Al-Muddassir', 'min' => 1, 'max' => 56],
                ['nama' => 'Al-Qiyamah', 'min' => 1, 'max' => 40],
                ['nama' => 'Al-Insan', 'min' => 1, 'max' => 31],
                ['nama' => 'Al-Mursalat', 'min' => 1, 'max' => 50]
            ],
            30 => [
                ['nama' => 'An-Naba\'', 'min' => 1, 'max' => 40],
                ['nama' => 'An-Nazi\'at', 'min' => 1, 'max' => 46],
                ['nama' => 'Abasa', 'min' => 1, 'max' => 42],
                ['nama' => 'At-Takwir', 'min' => 1, 'max' => 29],
                ['nama' => 'Al-Infitar', 'min' => 1, 'max' => 19],
                ['nama' => 'Al-Mutaffifin', 'min' => 1, 'max' => 36],
                ['nama' => 'Al-Inshiqaq', 'min' => 1, 'max' => 25],
                ['nama' => 'Al-Buruj', 'min' => 1, 'max' => 22],
                ['nama' => 'At-Tariq', 'min' => 1, 'max' => 17],
                ['nama' => 'Al-A\'la', 'min' => 1, 'max' => 19],
                ['nama' => 'Al-Gasiyah', 'min' => 1, 'max' => 26],
                ['nama' => 'Al-Fajr', 'min' => 1, 'max' => 30],
                ['nama' => 'Al-Balad', 'min' => 1, 'max' => 20],
                ['nama' => 'Ash-Shams', 'min' => 1, 'max' => 15],
                ['nama' => 'Al-Lail', 'min' => 1, 'max' => 21],
                ['nama' => 'Ad-Duha', 'min' => 1, 'max' => 11],
                ['nama' => 'Al-Inshirah', 'min' => 1, 'max' => 8],
                ['nama' => 'At-Tin', 'min' => 1, 'max' => 8],
                ['nama' => 'Al-Alaq', 'min' => 1, 'max' => 19],
                ['nama' => 'Al-Qadr', 'min' => 1, 'max' => 5],
                ['nama' => 'Al-Bayyinah', 'min' => 1, 'max' => 8],
                ['nama' => 'Az-Zazalah', 'min' => 1, 'max' => 8],
                ['nama' => 'Al-Adiyat', 'min' => 1, 'max' => 11],
                ['nama' => 'Al-Qari\'ah', 'min' => 1, 'max' => 11],
                ['nama' => 'At-Takasur', 'min' => 1, 'max' => 8],
                ['nama' => 'Al-Asr', 'min' => 1, 'max' => 3],
                ['nama' => 'Al-Humazah', 'min' => 1, 'max' => 9],
                ['nama' => 'Al-Fil', 'min' => 1, 'max' => 5],
                ['nama' => 'Quraisy', 'min' => 1, 'max' => 4],
                ['nama' => 'Al-Ma\'un', 'min' => 1, 'max' => 7],
                ['nama' => 'Al-Kautsar', 'min' => 1, 'max' => 3],
                ['nama' => 'Al-Kafirun', 'min' => 1, 'max' => 6],
                ['nama' => 'An-Nasr', 'min' => 1, 'max' => 3],
                ['nama' => 'Al-Lahab', 'min' => 1, 'max' => 5],
                ['nama' => 'Al-Ikhlas', 'min' => 1, 'max' => 4],
                ['nama' => 'Al-Falaq', 'min' => 1, 'max' => 5],
                ['nama' => 'An-Nas', 'min' => 1, 'max' => 6]
            ]
        ];

        $surahList = $mappingJuz[$juz] ?? [];

        $result = [];
        foreach ($surahList as $s) {
            $result[] = [
                'nama_surah' => $s['nama'],
                'ayat_mulai_default' => $s['min'],
                'ayat_selesai_default' => $s['max'],
                'jumlah_ayat' => $s['max']
            ];
        }

        return $result;
    }

    // Helper data master 114 Surah lengkap dengan informasi Juz dan jumlah ayatnya
    private function getMasterSurahAlquran()
    {
        return [
            ['nama' => 'Al-Fatihah', 'jumlah_ayat' => 7, 'juz_mulai' => 1, 'juz_selesai' => 1, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 7],
            ['nama' => 'Al-Baqarah', 'jumlah_ayat' => 286, 'juz_mulai' => 1, 'juz_selesai' => 3, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 286],
            ['nama' => 'Ali \'Imran', 'jumlah_ayat' => 200, 'juz_mulai' => 3, 'juz_selesai' => 4, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 200],
            ['nama' => 'An-Nisa\'', 'jumlah_ayat' => 176, 'juz_mulai' => 4, 'juz_selesai' => 6, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 176],
            ['nama' => 'Al-Ma\'idah', 'jumlah_ayat' => 120, 'juz_mulai' => 6, 'juz_selesai' => 7, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 120],
            ['nama' => 'Al-An\'am', 'jumlah_ayat' => 165, 'juz_mulai' => 7, 'juz_selesai' => 8, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 165],
            ['nama' => 'Al-A\'raf', 'jumlah_ayat' => 206, 'juz_mulai' => 8, 'juz_selesai' => 9, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 206],
            ['nama' => 'Al-Anfal', 'jumlah_ayat' => 75, 'juz_mulai' => 9, 'juz_selesai' => 10, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 75],
            ['nama' => 'At-Taubah', 'jumlah_ayat' => 129, 'juz_mulai' => 10, 'juz_selesai' => 11, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 129],
            ['nama' => 'Yunus', 'jumlah_ayat' => 109, 'juz_mulai' => 11, 'juz_selesai' => 11, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 109],
            ['nama' => 'Hud', 'jumlah_ayat' => 123, 'juz_mulai' => 11, 'juz_selesai' => 12, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 123],
            ['nama' => 'Yusuf', 'jumlah_ayat' => 111, 'juz_mulai' => 12, 'juz_selesai' => 13, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 111],
            ['nama' => 'Ar-Ra\'d', 'jumlah_ayat' => 43, 'juz_mulai' => 13, 'juz_selesai' => 13, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 43],
            ['nama' => 'Ibrahim', 'jumlah_ayat' => 52, 'juz_mulai' => 13, 'juz_selesai' => 13, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 52],
            ['nama' => 'Al-Hijr', 'jumlah_ayat' => 99, 'juz_mulai' => 14, 'juz_selesai' => 14, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 99],
            ['nama' => 'An-Nahl', 'jumlah_ayat' => 128, 'juz_mulai' => 14, 'juz_selesai' => 14, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 128],
            ['nama' => 'Al-Isra\'', 'jumlah_ayat' => 111, 'juz_mulai' => 15, 'juz_selesai' => 15, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 111],
            ['nama' => 'Al-Kahf', 'jumlah_ayat' => 110, 'juz_mulai' => 15, 'juz_selesai' => 16, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 110],
            ['nama' => 'Maryam', 'jumlah_ayat' => 98, 'juz_mulai' => 16, 'juz_selesai' => 16, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 98],
            ['nama' => 'Taha', 'jumlah_ayat' => 135, 'juz_mulai' => 16, 'juz_selesai' => 16, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 135],
            ['nama' => 'Al-Anbiya\'', 'jumlah_ayat' => 112, 'juz_mulai' => 17, 'juz_selesai' => 17, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 112],
            ['nama' => 'Al-Hajj', 'jumlah_ayat' => 78, 'juz_mulai' => 17, 'juz_selesai' => 17, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 78],
            ['nama' => 'Al-Mu\'minun', 'jumlah_ayat' => 118, 'juz_mulai' => 18, 'juz_selesai' => 18, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 118],
            ['nama' => 'An-Nur', 'jumlah_ayat' => 64, 'juz_mulai' => 18, 'juz_selesai' => 18, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 64],
            ['nama' => 'Al-Furqan', 'jumlah_ayat' => 77, 'juz_mulai' => 18, 'juz_selesai' => 19, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 77],
            ['nama' => 'Ash-Shu\'ara\'', 'jumlah_ayat' => 227, 'juz_mulai' => 19, 'juz_selesai' => 19, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 227],
            ['nama' => 'An-Naml', 'jumlah_ayat' => 93, 'juz_mulai' => 19, 'juz_selesai' => 20, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 93],
            ['nama' => 'Al-Qasas', 'jumlah_ayat' => 88, 'juz_mulai' => 20, 'juz_selesai' => 20, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 88],
            ['nama' => 'Al-Ankabut', 'jumlah_ayat' => 69, 'juz_mulai' => 20, 'juz_selesai' => 21, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 69],
            ['nama' => 'Ar-Rum', 'jumlah_ayat' => 60, 'juz_mulai' => 21, 'juz_selesai' => 21, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 60],
            ['nama' => 'Luqman', 'jumlah_ayat' => 34, 'juz_mulai' => 21, 'juz_selesai' => 21, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 34],
            ['nama' => 'As-Sajdah', 'jumlah_ayat' => 30, 'juz_mulai' => 21, 'juz_selesai' => 21, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 30],
            ['nama' => 'Al-Ahzab', 'jumlah_ayat' => 73, 'juz_mulai' => 21, 'juz_selesai' => 22, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 73],
            ['nama' => 'Saba\'', 'jumlah_ayat' => 54, 'juz_mulai' => 22, 'juz_selesai' => 22, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 54],
            ['nama' => 'Fatir', 'jumlah_ayat' => 45, 'juz_mulai' => 22, 'juz_selesai' => 22, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 45],
            ['nama' => 'Ya-Sin', 'jumlah_ayat' => 83, 'juz_mulai' => 22, 'juz_selesai' => 23, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 83],
            ['nama' => 'As-Saffat', 'jumlah_ayat' => 182, 'juz_mulai' => 23, 'juz_selesai' => 23, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 182],
            ['nama' => 'Sad', 'jumlah_ayat' => 88, 'juz_mulai' => 23, 'juz_selesai' => 23, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 88],
            ['nama' => 'Az-Zumar', 'jumlah_ayat' => 75, 'juz_mulai' => 23, 'juz_selesai' => 24, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 75],
            ['nama' => 'Gafir', 'jumlah_ayat' => 85, 'juz_mulai' => 24, 'juz_selesai' => 24, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 85],
            ['nama' => 'Fussilat', 'jumlah_ayat' => 54, 'juz_mulai' => 24, 'juz_selesai' => 25, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 54],
            ['nama' => 'Ash-Shura', 'jumlah_ayat' => 53, 'juz_mulai' => 25, 'juz_selesai' => 25, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 53],
            ['nama' => 'Az-Zukhruf', 'jumlah_ayat' => 89, 'juz_mulai' => 25, 'juz_selesai' => 25, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 89],
            ['nama' => 'Ad-Dukhan', 'jumlah_ayat' => 59, 'juz_mulai' => 25, 'juz_selesai' => 25, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 59],
            ['nama' => 'Al-Jasiyah', 'jumlah_ayat' => 37, 'juz_mulai' => 25, 'juz_selesai' => 25, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 37],
            ['nama' => 'Al-Ahqaf', 'jumlah_ayat' => 35, 'juz_mulai' => 26, 'juz_selesai' => 26, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 35],
            ['nama' => 'Muhammad', 'jumlah_ayat' => 38, 'juz_mulai' => 26, 'juz_selesai' => 26, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 38],
            ['nama' => 'Al-Fath', 'jumlah_ayat' => 29, 'juz_mulai' => 26, 'juz_selesai' => 26, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 29],
            ['nama' => 'Al-Hujurat', 'jumlah_ayat' => 18, 'juz_mulai' => 26, 'juz_selesai' => 26, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 18],
            ['nama' => 'Qaf', 'jumlah_ayat' => 45, 'juz_mulai' => 26, 'juz_selesai' => 26, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 45],
            ['nama' => 'Ad-Zariyat', 'jumlah_ayat' => 60, 'juz_mulai' => 26, 'juz_selesai' => 27, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 60],
            ['nama' => 'At-Tur', 'jumlah_ayat' => 49, 'juz_mulai' => 27, 'juz_selesai' => 27, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 49],
            ['nama' => 'An-Najm', 'jumlah_ayat' => 62, 'juz_mulai' => 27, 'juz_selesai' => 27, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 62],
            ['nama' => 'Al-Qamar', 'jumlah_ayat' => 55, 'juz_mulai' => 27, 'juz_selesai' => 27, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 55],
            ['nama' => 'Ar-Rahman', 'jumlah_ayat' => 78, 'juz_mulai' => 27, 'juz_selesai' => 27, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 78],
            ['nama' => 'Al-Waqi\'ah', 'jumlah_ayat' => 96, 'juz_mulai' => 27, 'juz_selesai' => 27, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 96],
            ['nama' => 'Al-Hadid', 'jumlah_ayat' => 29, 'juz_mulai' => 27, 'juz_selesai' => 27, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 29],
            ['nama' => 'Al-Mujadilah', 'jumlah_ayat' => 22, 'juz_mulai' => 28, 'juz_selesai' => 28, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 22],
            ['nama' => 'Al-Hasyr', 'jumlah_ayat' => 24, 'juz_mulai' => 28, 'juz_selesai' => 28, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 24],
            ['nama' => 'Al-Mumtahanah', 'jumlah_ayat' => 13, 'juz_mulai' => 28, 'juz_selesai' => 28, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 13],
            ['nama' => 'As-Saff', 'jumlah_ayat' => 14, 'juz_mulai' => 28, 'juz_selesai' => 28, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 14],
            ['nama' => 'Al-Jumu\'ah', 'jumlah_ayat' => 11, 'juz_mulai' => 28, 'juz_selesai' => 28, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 11],
            ['nama' => 'Al-Munafiqun', 'jumlah_ayat' => 11, 'juz_mulai' => 28, 'juz_selesai' => 28, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 11],
            ['nama' => 'At-Tagabun', 'jumlah_ayat' => 18, 'juz_mulai' => 28, 'juz_selesai' => 28, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 18],
            ['nama' => 'At-Talaq', 'jumlah_ayat' => 12, 'juz_mulai' => 28, 'juz_selesai' => 28, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 12],
            ['nama' => 'At-Tahrim', 'jumlah_ayat' => 12, 'juz_mulai' => 28, 'juz_selesai' => 28, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 12],
            ['nama' => 'Al-Mulk', 'jumlah_ayat' => 30, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 30],
            ['nama' => 'Al-Qalam', 'jumlah_ayat' => 52, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 52],
            ['nama' => 'Al-Haqqah', 'jumlah_ayat' => 52, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 52],
            ['nama' => 'Al-Ma\'arij', 'jumlah_ayat' => 44, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 44],
            ['nama' => 'Nuh', 'jumlah_ayat' => 28, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 28],
            ['nama' => 'Al-Jinn', 'jumlah_ayat' => 28, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 28],
            ['nama' => 'Al-Muzzammil', 'jumlah_ayat' => 20, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 20],
            ['nama' => 'Al-Muddassir', 'jumlah_ayat' => 56, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 56],
            ['nama' => 'Al-Qiyamah', 'jumlah_ayat' => 40, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 40],
            ['nama' => 'Al-Insan', 'jumlah_ayat' => 31, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 31],
            ['nama' => 'Al-Mursalat', 'jumlah_ayat' => 50, 'juz_mulai' => 29, 'juz_selesai' => 29, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 50],
            ['nama' => 'An-Naba\'', 'jumlah_ayat' => 40, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 40],
            ['nama' => 'An-Nazi\'at', 'jumlah_ayat' => 46, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 46],
            ['nama' => 'Abasa', 'jumlah_ayat' => 42, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 42],
            ['nama' => 'At-Takwir', 'jumlah_ayat' => 29, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 29],
            ['nama' => 'Al-Infitar', 'jumlah_ayat' => 19, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 19],
            ['nama' => 'Al-Mutaffifin', 'jumlah_ayat' => 36, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 36],
            ['nama' => 'Al-Inshiqaq', 'jumlah_ayat' => 25, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 25],
            ['nama' => 'Al-Buruj', 'jumlah_ayat' => 22, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 22],
            ['nama' => 'At-Tariq', 'jumlah_ayat' => 17, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 17],
            ['nama' => 'Al-A\'la', 'jumlah_ayat' => 19, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 19],
            ['nama' => 'Al-Gasiyah', 'jumlah_ayat' => 26, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 26],
            ['nama' => 'Al-Fajr', 'jumlah_ayat' => 30, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 30],
            ['nama' => 'Al-Balad', 'jumlah_ayat' => 20, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 20],
            ['nama' => 'Ash-Shams', 'jumlah_ayat' => 15, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 15],
            ['nama' => 'Al-Lail', 'jumlah_ayat' => 21, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 21],
            ['nama' => 'Ad-Duha', 'jumlah_ayat' => 11, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 11],
            ['nama' => 'Al-Inshirah', 'jumlah_ayat' => 8, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 8],
            ['nama' => 'At-Tin', 'jumlah_ayat' => 8, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 8],
            ['nama' => 'Al-Alaq', 'jumlah_ayat' => 19, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 19],
            ['nama' => 'Al-Qadr', 'jumlah_ayat' => 5, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 5],
            ['nama' => 'Al-Bayyinah', 'jumlah_ayat' => 8, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 8],
            ['nama' => 'Az-Zazalah', 'jumlah_ayat' => 8, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 8],
            ['nama' => 'Al-Adiyat', 'jumlah_ayat' => 11, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 11],
            ['nama' => 'Al-Qari\'ah', 'jumlah_ayat' => 11, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 11],
            ['nama' => 'At-Takasur', 'jumlah_ayat' => 8, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 8],
            ['nama' => 'Al-Asr', 'jumlah_ayat' => 3, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 3],
            ['nama' => 'Al-Humazah', 'jumlah_ayat' => 9, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 9],
            ['nama' => 'Al-Fil', 'jumlah_ayat' => 5, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 5],
            ['nama' => 'Quraisy', 'jumlah_ayat' => 4, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 4],
            ['nama' => 'Al-Ma\'un', 'jumlah_ayat' => 7, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 7],
            ['nama' => 'Al-Kautsar', 'jumlah_ayat' => 3, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 3],
            ['nama' => 'Al-Kafirun', 'jumlah_ayat' => 6, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 6],
            ['nama' => 'An-Idr', 'jumlah_ayat' => 3, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 3], // An-Nasr
            ['nama' => 'Al-Lahab', 'jumlah_ayat' => 5, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 5],
            ['nama' => 'Al-Ikhlas', 'jumlah_ayat' => 4, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 4],
            ['nama' => 'Al-Falaq', 'jumlah_ayat' => 5, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 5],
            ['nama' => 'An-Nas', 'jumlah_ayat' => 6, 'juz_mulai' => 30, 'juz_selesai' => 30, 'ayat_mulai_asli' => 1, 'ayat_selesai_asli' => 6],
        ];
    }


    // ==========================================
    // GLOBAL DI DASHBOARD ADMIN    
    // ==========================================

    // Contoh fungsi pendukung di HafalanModel.php untuk Admin
    public function getRataRataGlobal($periode = 'tahun_ini')
    {
        // Tambahkan join ke santri dan filter status
        $builder = $this->db->table('hafalan');
        $builder->join('santri', 'santri.id = hafalan.id_santri');
        $builder->where('santri.status_aktif', 'Aktif');
        $this->applyPeriodeFilter($builder, $periode);

        $rows = $builder->select('ayat_mulai, ayat_selesai')->get()->getResultArray();

        $totalAyat = 0;
        foreach ($rows as $row) {
            $mulai = (int) ($row['ayat_mulai'] ?? 0);
            $selesai = (int) ($row['ayat_selesai'] ?? 0);
            if ($selesai >= $mulai) {
                $totalAyat += ($selesai - $mulai + 1);
            }
        }

        $builderCount = $this->db->table('hafalan');
        $builderCount->join('santri', 'santri.id = hafalan.id_santri');
        $builderCount->where('santri.status_aktif', 'Aktif');
        $this->applyPeriodeFilter($builderCount, $periode);
        $totalSetoran = $builderCount->countAllResults();

        return ($totalSetoran > 0) ? (int) round($totalAyat / $totalSetoran) : 0;
    }

    public function getJuzDominanGlobal($periode = 'tahun_ini')
    {
        $progressJuz = $this->getProgressJuzGlobal($periode);

        if (empty($progressJuz) || $progressJuz[0]['nama'] === 'Belum ada data setoran') {
            return ['juz' => '-', 'persen' => 0];
        }

        $teratas = $progressJuz[0];

        $nomorJuz = str_replace('Juz ', '', $teratas['nama']);

        return [
            'juz' => $nomorJuz,
            'persen' => $teratas['persen']
        ];
    }

    public function getPredikatTerbanyakGlobal($periode = 'tahun_ini')
    {
        $builder = $this->db->table('hafalan');
        $builder->join('santri', 'santri.id = hafalan.id_santri');
        $builder->where('santri.status_aktif', 'Aktif');
        $this->applyPeriodeFilter($builder, $periode);

        $result = $builder->select('predikat, COUNT(predikat) as jumlah')
            ->groupBy('predikat')
            ->orderBy('jumlah', 'DESC')
            ->get()
            ->getRowArray();

        return ($result && !empty($result['predikat'])) ? ucwords($result['predikat']) : 'Belum Ada';
    }

    public function getProgressJuzGlobal($periode = 'tahun_ini')
    {
        $builder = $this->db->table($this->table);
        $builder->join('santri', 'santri.id = hafalan.id_santri');
        $builder->where('santri.status_aktif', 'Aktif');

        if ($periode == 'bulan_ini') {
            $builder->where('MONTH(hafalan.created_at)', date('m'));
            $builder->where('YEAR(hafalan.created_at)', date('Y'));
        } elseif ($periode == 'bulan_lalu') {
            $builder->where('MONTH(hafalan.created_at)', date('m', strtotime('-1 month')));
            $builder->where('YEAR(hafalan.created_at)', date('Y', strtotime('-1 month')));
        } elseif ($periode == 'tahun_lalu') {
            $builder->where('YEAR(hafalan.created_at)', date('Y', strtotime('-1 year')));
        } else {
            $builder->where('YEAR(hafalan.created_at)', date('Y'));
        }

        $builder->select("juz, SUM((ayat_selesai - ayat_mulai) + 1) as total_ayat_juz");
        $builder->groupBy("juz");
        $result = $builder->get()->getResultArray();

        $grandTotalAyat = array_sum(array_column($result, 'total_ayat_juz'));

        $dataMentah = [];
        foreach ($result as $row) {
            $juzAngka = $row['juz'];
            $jumlahAyat = (int) $row['total_ayat_juz'];
            $persen = ($grandTotalAyat > 0) ? round(($jumlahAyat / $grandTotalAyat) * 100) : 0;

            $dataMentah[] = [
                'nama' => 'Juz ' . $juzAngka,
                'persen' => $persen,
                'total' => $jumlahAyat
            ];
        }

        usort($dataMentah, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        $warnaList = ['success', 'primary', 'warning', 'info', 'danger', 'secondary'];
        $output = [];

        foreach ($dataMentah as $index => $item) {
            $output[] = [
                'nama' => $item['nama'],
                'persen' => $item['persen'],
                'color' => $warnaList[$index % count($warnaList)]
            ];
        }

        if (empty($output)) {
            $output = [
                ['nama' => 'Belum ada data setoran', 'persen' => 0, 'color' => 'secondary']
            ];
        }

        return $output;
    }

    public function getGrafikSetoranGlobal($periode = 'tahun_ini')
    {
        $labels = [];
        $values = [];

        // FILTER MINGGU INI -> Tampilkan per Hari dalam Minggu Ini (Senin - Minggu)
        if ($periode == 'minggu_ini') {
            // Tentukan awal minggu (Senin) dan akhir minggu (Minggu) ini secara presisi
            $startOfWeek = date('Y-m-d', strtotime('monday this week'));
            $endOfWeek = date('Y-m-d', strtotime('sunday this week'));

            $builder = $this->db->table($this->table);
            $builder->join('santri', 'santri.id = hafalan.id_santri');
            $builder->where('santri.status_aktif', 'Aktif');
            $builder->select("DATE(hafalan.created_at) as tanggal, COUNT(DISTINCT id_santri) as total_santri");
            $builder->where('DATE(hafalan.created_at) >=', $startOfWeek);
            $builder->where('DATE(hafalan.created_at) <=', $endOfWeek);
            $builder->groupBy("DATE(hafalan.created_at)");
            $result = $builder->get()->getResultArray();

            $dataPerHari = [];
            $translation = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu'
            ];

            foreach ($result as $row) {
                $hariInggris = date('l', strtotime($row['tanggal']));
                $hariIndo = $translation[$hariInggris] ?? $hariInggris;
                $dataPerHari[$hariIndo] = (int) $row['total_santri'];
            }

            $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            foreach ($daftarHari as $hari) {
                $labels[] = $hari;
                $values[] = $dataPerHari[$hari] ?? 0;
            }
        } elseif ($periode == 'minggu_lalu') {
            // Tentukan awal dan akhir minggu lalu secara presisi
            $startOfWeek = date('Y-m-d', strtotime('monday last week'));
            $endOfWeek = date('Y-m-d', strtotime('sunday last week'));

            $builder = $this->db->table($this->table);
            $builder->join('santri', 'santri.id = hafalan.id_santri');
            $builder->where('santri.status_aktif', 'Aktif');
            $builder->select("DATE(hafalan.created_at) as tanggal, COUNT(DISTINCT id_santri) as total_santri");
            $builder->where('DATE(hafalan.created_at) >=', $startOfWeek);
            $builder->where('DATE(hafalan.created_at) <=', $endOfWeek);
            $builder->groupBy("DATE(hafalan.created_at)");
            $result = $builder->get()->getResultArray();

            $dataPerHari = [];
            $translation = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu'
            ];

            foreach ($result as $row) {
                $hariInggris = date('l', strtotime($row['tanggal']));
                $hariIndo = $translation[$hariInggris] ?? $hariInggris;
                $dataPerHari[$hariIndo] = (int) $row['total_santri'];
            }

            $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            foreach ($daftarHari as $hari) {
                $labels[] = $hari;
                $values[] = $dataPerHari[$hari] ?? 0;
            }
        }
        // FILTER BULAN INI / BULAN LALU -> Tampilkan per Tanggal (1 sampai 30/31)
        elseif ($periode == 'bulan_ini' || $periode == 'bulan_lalu') {
            $targetBulan = ($periode == 'bulan_ini') ? date('m') : date('m', strtotime('-1 month'));
            $targetTahun = ($periode == 'bulan_ini') ? date('Y') : date('Y', strtotime('-1 month'));
            $jumlahHari = cal_days_in_month(CAL_GREGORIAN, (int) $targetBulan, (int) $targetTahun);

            $builder = $this->db->table($this->table);
            $builder->join('santri', 'santri.id = hafalan.id_santri');
            $builder->where('santri.status_aktif', 'Aktif');
            $builder->select("DAY(hafalan.created_at) as hari_angka, COUNT(DISTINCT id_santri) as total_santri");
            $builder->where('MONTH(hafalan.created_at)', $targetBulan);
            $builder->where('YEAR(hafalan.created_at)', $targetTahun);
            $builder->groupBy("DAY(hafalan.created_at)");
            $result = $builder->get()->getResultArray();

            $dataPerHari = [];
            foreach ($result as $row) {
                $dataPerHari[(int) $row['hari_angka']] = (int) $row['total_santri'];
            }

            for ($i = 1; $i <= $jumlahHari; $i++) {
                $labels[] = $i;
                $values[] = $dataPerHari[$i] ?? 0;
            }
        }
        // FILTER TAHUN INI / TAHUN LALU -> Tampilkan 12 Bulan (Januari - Desember)
        else {
            $tahunDipilih = ($periode == 'tahun_lalu') ? date('Y') - 1 : date('Y');

            $builder = $this->db->table($this->table);
            $builder->join('santri', 'santri.id = hafalan.id_santri');
            $builder->where('santri.status_aktif', 'Aktif');
            $builder->select("MONTH(hafalan.created_at) as bulan_angka, COUNT(DISTINCT id_santri) as total_santri");
            $builder->where('YEAR(hafalan.created_at)', $tahunDipilih);
            $builder->groupBy("MONTH(hafalan.created_at)");
            $result = $builder->get()->getResultArray();

            $dataPerBulan = [];
            foreach ($result as $row) {
                $dataPerBulan[(int) $row['bulan_angka']] = (int) $row['total_santri'];
            }

            $namaBulan = [
                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'Mei',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Agu',
                9 => 'Sep',
                10 => 'Okt',
                11 => 'Nov',
                12 => 'Des'
            ];

            for ($i = 1; $i <= 12; $i++) {
                $labels[] = $namaBulan[$i];
                $values[] = $dataPerBulan[$i] ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    // -------------------------------------------------------------------------

    // Fungsi untuk mengambil data hafalan lengkap dengan relasi santri dan guru
    public function getHafalanWithRelations($id = null)
    {
        $this->select('hafalan.*, santri.nama_santri, santri.foto AS foto_santri, guru.nama_guru, kelas.nama_kelas AS nama_kelas')
            ->join('santri', 'santri.id = hafalan.id_santri', 'left')
            ->join('guru', 'guru.id = hafalan.id_guru', 'left')
            ->join('kelas', 'kelas.id = santri.id_kelas', 'left');

        if ($id) {
            return $this->where('hafalan.id', $id)->first();
        }

        return $this->orderBy('hafalan.created_at', 'DESC');
    }

    // Fungsi khusus untuk statistik
    public function getStatistikHafalan($id_santri = null)
    {
        $builder = $this->db->table('hafalan');
        $builder->select('surah, COUNT(*) as jumlah_setoran');
        if ($id_santri) {
            $builder->where('id_santri', $id_santri);
        }
        $builder->groupBy('surah');
        return $builder->get()->getResultArray();
    }

    public function getHafalanByGuru($idGuru)
    {
        return $this->select('hafalan.*, santri.nama_santri, santri.foto, santri.nis, guru.nama_guru')
            ->join('santri', 'santri.id = hafalan.id_santri')
            ->join('guru', 'guru.id = hafalan.id_guru')
            ->where('hafalan.id_guru', $idGuru)
            ->orderBy('hafalan.created_at', 'DESC');
    }

    public function getRiwayatHafalan()
    {
        return $this->select('hafalan.*, santri.nama_santri')
            ->join('santri', 'santri.id = hafalan.id_santri', 'left')
            ->orderBy('hafalan.created_at', 'DESC')
            ->findAll();
    }

    // ==========================================
    // HELPER FILTER PERIODE
    // ==========================================
    private function applyPeriodeFilter($builder, $periode)
    {
        $tahunIni = date('Y');
        $tahunLalu = $tahunIni - 1;

        if ($periode == 'minggu_ini') {
            $builder->where('hafalan.created_at >=', date('Y-m-d', strtotime('monday this week')))
                ->where('hafalan.created_at <=', date('Y-m-d 23:59:59'));
        } elseif ($periode == 'bulan_ini') {
            $builder->where('MONTH(hafalan.created_at)', date('m'))
                ->where('YEAR(hafalan.created_at)', $tahunIni);
        } elseif ($periode == 'semester_ini') {
            $bulanIni = date('n');
            if ($bulanIni >= 1 && $bulanIni <= 6) {
                $builder->where('hafalan.created_at >=', "$tahunIni-01-01")
                    ->where('hafalan.created_at <=', "$tahunIni-06-30");
            } else {
                $builder->where('hafalan.created_at >=', "$tahunIni-07-01")
                    ->where('hafalan.created_at <=', "$tahunIni-12-31");
            }
        } elseif ($periode == 'tahun_lalu') {
            $builder->where('YEAR(hafalan.created_at)', $tahunLalu);
        } else {
            $builder->where('YEAR(hafalan.created_at)', $tahunIni);
        }

        return $builder;
    }

    // ==========================================
    // STATISTIK KELAS (DENGAN FILTER PERIODE)
    // ==========================================

    public function getRataRataKelas($id_guru, $periode = 'bulan_ini')
    {
        if (!$id_guru)
            return 0;

        $builder = $this->select("AVG((ayat_selesai - ayat_mulai) + 1) as rata_rata")
            ->where('id_guru', $id_guru);

        $this->applyPeriodeFilter($builder, $periode);
        $result = $builder->first();

        return round($result['rata_rata'] ?? 0);
    }

    public function getJuzDominanKelas($id_guru, $periode = 'bulan_ini')
    {
        if (!$id_guru) {
            return ['nama' => 'Juz 30', 'persentase' => 0];
        }

        $progressJuz = $this->getProgressJuzKelas($id_guru, $periode);

        if (empty($progressJuz) || (isset($progressJuz[0]['nama']) && $progressJuz[0]['nama'] === 'Belum ada data setoran')) {
            return ['nama' => '-', 'persentase' => 0];
        }

        // Ambil data teratas dari progress bar (yang paling tinggi persentasenya)
        $teratas = $progressJuz[0];

        return [
            'nama' => $teratas['nama'] ?? ('Juz ' . ($teratas['juz'] ?? '30')),
            'persentase' => $teratas['persen'] ?? $teratas['persentase'] ?? 0
        ];
    }

    public function getPredikatTerbanyakKelas($id_guru, $periode = 'bulan_ini')
    {
        if (!$id_guru)
            return ['predikat' => 'Mumtaz', 'keterangan' => 'Sangat Baik'];

        $builder = $this->select('predikat, COUNT(*) as total')
            ->where('id_guru', $id_guru);

        $this->applyPeriodeFilter($builder, $periode);
        $query = $builder->groupBy('predikat')
            ->orderBy('total', 'DESC')
            ->first();

        $predikat = $query['predikat'] ?? 'Mumtaz';

        $ket = 'Sangat Baik';
        if ($predikat == 'Jayyid Jiddan')
            $ket = 'Baik Sekali';
        elseif ($predikat == 'Jayyid')
            $ket = 'Baik';

        return ['predikat' => $predikat, 'keterangan' => $ket];
    }

    public function getProgressJuzKelas($id_guru, $periode = 'bulan_ini')
    {
        if (!$id_guru)
            return [];

        // Ambil data jumlah per juz
        $builder = $this->select('juz, COUNT(*) as jumlah')
            ->where('id_guru', $id_guru);
        $this->applyPeriodeFilter($builder, $periode);
        $result = $builder->groupBy('juz')->orderBy('jumlah', 'DESC')->findAll();

        if (empty($result))
            return [];

        // Hitung total keseluruhan setoran pada periode tersebut
        $builderTotal = $this->where('id_guru', $id_guru);
        $this->applyPeriodeFilter($builderTotal, $periode);
        $totalSemua = $builderTotal->countAllResults(false);

        // Masukkan persentase proporsional ke dalam array
        foreach ($result as &$row) {
            $row['persen'] = $totalSemua > 0 ? round(($row['jumlah'] / $totalSemua) * 100) : 0;
        }

        return $result;
    }

    public function getGrafikSetoranKelas($id_guru, $periode = 'bulan_ini')
    {
        if (!$id_guru)
            return [];

        $builder = $this->select("DATE(created_at) as created_at, COUNT(*) as total")
            ->where('id_guru', $id_guru);

        $this->applyPeriodeFilter($builder, $periode);
        return $builder->groupBy("DATE(created_at)")
            ->orderBy("DATE(created_at)", "ASC")
            ->findAll();
    }

    // Method untuk mengambil rincian data laporan cetak
    public function getDetailHafalanByPeriode($id_guru, $periode = 'bulan_ini')
    {
        if (!$id_guru)
            return [];

        $this->select('hafalan.*, santri.nama_santri');
        $this->join('santri', 'santri.id = hafalan.id_santri');
        $this->where('hafalan.id_guru', $id_guru);

        $this->applyPeriodeFilter($this, $periode);

        return $this->orderBy('hafalan.created_at', 'DESC')->findAll();
    }

    public function getRekapSantriKelas($id_guru, $periode = 'bulan_ini')
    {
        $builder = $this->db->table('hafalan');
        $builder->select('
        santri.nama_santri, 
        COUNT(hafalan.id) as frekuensi_setor, 
        SUM((hafalan.ayat_selesai - hafalan.ayat_mulai) + 1) as total_ayat,
        AVG((hafalan.ayat_selesai - hafalan.ayat_mulai) + 1) as rata_ayat,
        MAX(hafalan.juz) as juz_terakhir
    ');
        $builder->join('santri', 'santri.id = hafalan.id_santri');
        $builder->where('hafalan.id_guru', $id_guru);

        // Contoh filter periode sederhana
        if ($periode == 'bulan_ini') {
            $builder->where('MONTH(hafalan.created_at)', date('m'));
            $builder->where('YEAR(hafalan.created_at)', date('Y'));
        }

        $builder->groupBy('hafalan.id_santri');
        $builder->orderBy('total_ayat', 'DESC');

        return $builder->get()->getResultArray();
    }

    // ==========================================
    // --- Statistik Khusus Wali Santri ---
    // ==========================================

    public function getDetailHafalanSantriByPeriode($id_santri, $periode = 'bulan_ini')
    {
        if (!$id_santri)
            return [];
        $builder = $this->select('*')->where('id_santri', $id_santri);
        $this->applyPeriodeFilter($builder, $periode);
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function getTotalJuzSelesai($id_santri, $periode = 'bulan_ini')
    {
        if (!$id_santri)
            return 0;

        $builder = $this->select('COUNT(DISTINCT juz) as total_juz')
            ->where('id_santri', $id_santri);

        if ($periode == 'bulan_ini') {
            $builder->where('MONTH(created_at)', date('m'))
                ->where('YEAR(created_at)', date('Y'));
        } elseif ($periode == 'tahun_ini') {
            $builder->where('YEAR(created_at)', date('Y'));
        }

        $result = $builder->first();

        return $result['total_juz'] ?? 0;
    }

    public function getStreakHarian($id_santri)
    {
        if (!$id_santri)
            return 0;

        $setoran = $this->select('created_at')
            ->where('id_santri', $id_santri)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        if (empty($setoran))
            return 0;

        $tanggalUnik = [];
        foreach ($setoran as $row) {
            if (!empty($row['created_at'])) {
                $tgl = date('Y-m-d', strtotime($row['created_at']));
                $tanggalUnik[$tgl] = true;
            }
        }

        return count($tanggalUnik);
    }

    public function getRataPredikatSantri($id_santri)
    {
        if (!$id_santri)
            return ['predikat' => '-', 'keterangan' => '-'];

        $query = $this->select('predikat, COUNT(*) as total')
            ->where('id_santri', $id_santri)
            ->groupBy('predikat')
            ->orderBy('total', 'DESC')
            ->first();

        $predikat = $query['predikat'] ?? 'Mumtaz';
        return ['predikat' => $predikat, 'keterangan' => 'Sangat Baik'];
    }

    public function getKomposisiSetoran($id_santri)
    {
        if (!$id_santri)
            return ['ziyadah' => 0, 'murojaah' => 0];

        $total = $this->where('id_santri', $id_santri)->countAllResults();
        if ($total == 0)
            return ['ziyadah' => 0, 'murojaah' => 0];

        $ziyadah = $this->where('id_santri', $id_santri)->where('jenis', 'ziyadah')->countAllResults();

        $persenZiyadah = round(($ziyadah / $total) * 100);
        $persenMurojaah = 100 - $persenZiyadah;

        return [
            'ziyadah' => $persenZiyadah,
            'murojaah' => $persenMurojaah
        ];
    }

    public function getGrafikAyatBulanan($id_santri, $periode = 'bulan_ini')
    {
        $builder = $this->db->table('hafalan');
        $builder->select('DATE(created_at) as created_at, COUNT(*) as total');
        $builder->where('id_santri', $id_santri);

        // Filter berdasarkan periode
        if ($periode == 'minggu_ini') {
            $builder->where('created_at >=', date('Y-m-d', strtotime('-7 days')));
        } elseif ($periode == 'bulan_ini') {
            $builder->where('MONTH(created_at)', date('m'));
            $builder->where('YEAR(created_at)', date('Y'));
        } elseif ($periode == 'semester_ini') {
            $builder->where('created_at >=', date('Y-m-d', strtotime('-6 months')));
        }

        $builder->groupBy('DATE(created_at)');
        $builder->orderBy('created_at', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getDetailCapaianJuz($id_santri, $periode = 'bulan_ini')
    {
        if (!$id_santri)
            return [];

        $builder = $this->select('juz, MAX(surah) as surah, MIN(ayat_mulai) as ayat_mulai, MAX(ayat_selesai) as ayat_selesai, COUNT(id) as total_setoran, MAX(predikat) as predikat, MAX(created_at) as created_at, MAX(jenis) as jenis')
            ->where('id_santri', $id_santri);

        $this->applyPeriodeFilter($builder, $periode);

        return $builder->groupBy('juz')->findAll();
    }

    public function getGrafikAyatDuaGaris($id_santri, $periode)
    {
        $builder = $this->db->table('hafalan');
        $builder->select("DATE(created_at) as tanggal, 
                      SUM(CASE WHEN jenis = 'ziyadah' THEN (ayat_selesai - ayat_mulai + 1) ELSE 0 END) as ziyadah,
                      SUM(CASE WHEN jenis = 'murojaah' THEN (ayat_selesai - ayat_mulai + 1) ELSE 0 END) as murojaah");
        $builder->where('id_santri', $id_santri);

        if ($periode == 'bulan_ini') {
            $builder->where('MONTH(created_at)', date('m'));
            $builder->where('YEAR(created_at)', date('Y'));
        } elseif ($periode == 'minggu_ini') {
            $builder->where('created_at >=', date('Y-m-d', strtotime('-7 days')));
        }

        $builder->groupBy('DATE(created_at)');
        $builder->orderBy('tanggal', 'ASC');

        return $builder->get()->getResultArray();
    }


    // ==========================================
    // TAMBAHAN UNTUK RIWAYAT & STATISTIK WALI
    // ==========================================

    public function getRiwayatBySantri($idSantri, $periode = 'bulan_ini')
    {
        if (!$idSantri)
            return [];

        $builder = $this->select('hafalan.*, guru.nama_guru, guru.no_hp')
            ->join('guru', 'guru.id = hafalan.id_guru', 'left')
            ->where('hafalan.id_santri', $idSantri);

        $this->applyPeriodeFilter($builder, $periode);

        return $builder->orderBy('hafalan.created_at', 'DESC')->findAll();
    }

    public function getStatistikRingkasBySantri($idSantri, $periode = 'bulan_ini')
    {
        if (!$idSantri) {
            return [
                'juz_aktif' => '-',
                'total_setoran' => 0,
                'predikat_dominan' => '-'
            ];
        }

        $riwayatFiltered = $this->getRiwayatBySantri($idSantri, $periode);
        $totalSetoran = count($riwayatFiltered);

        $juzQuery = $this->select('juz')
            ->where('id_santri', $idSantri);
        $this->applyPeriodeFilter($juzQuery, $periode);
        $juzData = $juzQuery->orderBy('created_at', 'DESC')->first();
        $juzAktif = $juzData ? 'Juz ' . $juzData['juz'] : '-';

        $predikatData = $this->getRataPredikatSantri($idSantri);
        $predikatDominan = is_array($predikatData) ? ($predikatData['predikat'] ?? '-') : '-';

        return [
            'juz_aktif' => $juzAktif,
            'total_setoran' => $totalSetoran,
            'predikat_dominan' => $predikatDominan
        ];
    }
}
