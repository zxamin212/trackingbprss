<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Kantor;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cabang = Kantor::where('jenis', 'cabang')->first();

        // Admin sistem
        User::firstOrCreate(
            ['email' => 'admintracking@bprsahabatsejati.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Password123@'),
                'role' => 'admin',
                'kantor_id' => $cabang->id,
            ]
        );

        // Admin Legal
        User::firstOrCreate(
            ['email' => 'legaltracking@bprsahabatsejati.com'],
            [
                'name' => 'Admin Legal',
                'password' => Hash::make('Password123@'),
                'role' => 'admin_legal',
                'kantor_id' => $cabang->id,
            ]
        );

        // Direksi — Direktur Utama, Direktur Operasional, Komisaris
        $direksi = [
            ['nama' => 'Direktur Utama', 'email' => 'dirut@bprsahabatsejati.com'],
            ['nama' => 'Direktur Operasional', 'email' => 'diroperasional@bprsahabatsejati.com'],
            ['nama' => 'Komisaris', 'email' => 'komisaris@bprsahabatsejati.com'],
        ];

        foreach ($direksi as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nama'],
                    'password' => Hash::make('Password123@'),
                    'role' => 'direksi',
                    'kantor_id' => $cabang->id,
                ]
            );
        }

        // Manager Bisnis
        User::firstOrCreate(
            ['email' => 'managerbisnis@bprsahabatsejati.com'],
            [
                'name' => 'Manager Bisnis',
                'password' => Hash::make('Password123@'),
                'role' => 'manager_bisnis',
                'kantor_id' => $cabang->id,
            ]
        );

        // Area Manager — 1 per wilayah
        $areaManagers = [
            'barat'   => ['nama' => 'Area Manager Barat', 'email' => 'am.barat@bprsahabatsejati.com'],
            'selatan' => ['nama' => 'Area Manager Selatan', 'email' => 'am.selatan@bprsahabatsejati.com'],
            'timur'   => ['nama' => 'Area Manager Timur', 'email' => 'am.timur@bprsahabatsejati.com'],
        ];

        foreach ($areaManagers as $area => $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nama'],
                    'password' => Hash::make('Password123@'),
                    'role' => 'area_manager',
                    'area' => $area,
                    'kantor_id' => $cabang->id,
                ]
            );
        }

        // CS — 1 per SEMUA kantor (kas + pusat + cabang)
        Kantor::all()->each(function ($kantor) {
            $slug = Str::slug(str_replace(['Kantor Kas ', 'Kantor '], '', $kantor->nama_kantor));

            User::firstOrCreate(
                ['email' => "cs.{$slug}@bprsahabatsejati.com"],
                [
                    'name' => 'CS ' . $kantor->nama_kantor,
                    'password' => Hash::make('Password123@'),
                    'role' => 'cs',
                    'kantor_id' => $kantor->id,
                ]
            );
        });

        // SLO — 1 per SEMUA kantor (kas + pusat + cabang)
        Kantor::all()->each(function ($kantor) {
            $slug = Str::slug(str_replace(['Kantor Kas ', 'Kantor '], '', $kantor->nama_kantor));

            User::firstOrCreate(
                ['email' => "slo.{$slug}@bprsahabatsejati.com"],
                [
                    'name' => 'SLO ' . $kantor->nama_kantor,
                    'password' => Hash::make('Password123@'),
                    'role' => 'slo',
                    'kantor_id' => $kantor->id,
                ]
            );
        });
    }
}