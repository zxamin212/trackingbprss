<?php

namespace Database\Seeders;

use App\Models\JenisKredit;
use Illuminate\Database\Seeder;

class JenisKreditSeeder extends Seeder
{
    public function run(): void
    {
        $aktif = [
            'Kredit Makmur Sepeda Motor',
            'Kredit Makmur Mobil',
            'Kredit Makmur Sertifikat',
            'Kredit Solusi Sepeda Motor',
            'Kredit Solusi Mobil',
            'Kredit Solusi Sertifikat',
            'Kredit Pinjaman Eksekutif',
            'Kredit Fleksi',
            'Kredit Procim',
        ];

        foreach ($aktif as $i => $nama) {
            JenisKredit::firstOrCreate(['nama' => $nama], ['aktif' => true, 'urutan' => $i + 1]);
        }

        // Jenis lama: nonaktif, tetap ada supaya berkas lama masih punya padanan
        foreach (['Kredit Modal Usaha', 'Kredit Investasi', 'Kredit Konsumtif'] as $i => $nama) {
            JenisKredit::firstOrCreate(['nama' => $nama], ['aktif' => false, 'urutan' => 100 + $i]);
        }
    }
}