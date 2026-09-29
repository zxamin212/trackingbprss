<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kantor;

class KantorSeeder extends Seeder
{
    public function run(): void
    {
        Kantor::firstOrCreate(
            ['nama_kantor' => 'Kantor Cabang Pabuaran'],
            ['jenis' => 'cabang', 'area' => 'timur']
        );

        Kantor::firstOrCreate(
            ['nama_kantor' => 'Kantor Pusat'],
            ['jenis' => 'pusat', 'area' => 'barat']
        );

        $wilayah = [
            'barat'   => ['Kantor Kas Sumber', 'Kantor Kas Gegesik', 'Kantor Kas Arjawinangun', 'Kantor Kas Ciwaringin'],
            'selatan' => ['Kantor Kas Cangkoak', 'Kantor Kas Bobos', 'Kantor Kas Sedong', 'Kantor Kas Talun', 'Kantor Kas Beber'],
            'timur'   => ['Kantor Kas Sindang', 'Kantor Kas Karangsembung', 'Kantor Kas Waled', 'Kantor Kas Ciledug', 'Kantor Kas Pebadilan'],
        ];

        foreach ($wilayah as $area => $kantorList) {
            foreach ($kantorList as $nama) {
                Kantor::firstOrCreate(
                    ['nama_kantor' => $nama],
                    ['jenis' => 'kas', 'area' => $area]
                );
            }
        }
    }
}