<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kantor;

class KantorSeeder extends Seeder
{
    public function run(): void
    {
        Kantor::create([
            'nama_kantor' => 'Kantor Cabang Utama',
            'jenis' => 'cabang',
        ]);

        $kantorKas = [
            'Kantor Kas 1', 'Kantor Kas 2', 'Kantor Kas 3', 'Kantor Kas 4',
            'Kantor Kas 5', 'Kantor Kas 6', 'Kantor Kas 7', 'Kantor Kas 8',
            'Kantor Kas 9', 'Kantor Kas 10', 'Kantor Kas 11', 'Kantor Kas 12',
            'Kantor Kas 13', 'Kantor Kas 14',
        ];

        foreach ($kantorKas as $nama) {
            Kantor::create([
                'nama_kantor' => $nama,
                'jenis' => 'kas',
            ]);
        }
    }
}