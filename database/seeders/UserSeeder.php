<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kantor;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cabang = Kantor::where('jenis', 'cabang')->first();

        // Admin sistem
        User::create([
            'name' => 'Admin',
            'email' => 'admin@sahabatsejati.co.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'kantor_id' => $cabang->id,
        ]);

        // Senior Loan Officer & Admin Legal — terpusat di kantor cabang
        User::create([
            'name' => 'Senior Loan Officer',
            'email' => 'slo@sahabatsejati.co.id',
            'password' => Hash::make('password'),
            'role' => 'senior_loan_officer',
            'kantor_id' => $cabang->id,
        ]);

        User::create([
            'name' => 'Admin Legal',
            'email' => 'legal@sahabatsejati.co.id',
            'password' => Hash::make('password'),
            'role' => 'admin_legal',
            'kantor_id' => $cabang->id,
        ]);

        User::create([
            'name' => 'Direksi',
            'email' => 'direksi@sahabatsejati.co.id',
            'password' => Hash::make('password'),
            'role' => 'direksi',
            'kantor_id' => $cabang->id,
         ]);

        // CS — satu per kantor kas
        Kantor::where('jenis', 'kas')->get()->each(function ($kantor, $i) {
            User::create([
                'name' => 'CS ' . $kantor->nama_kantor,
                'email' => 'cs' . ($i + 1) . '@sahabatsejati.co.id',
                'password' => Hash::make('password'),
                'role' => 'cs',
                'kantor_id' => $kantor->id,
            ]);
        });
    }
}