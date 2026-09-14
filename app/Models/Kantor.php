<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kantor extends Model
{
    use HasFactory;

    protected $table = 'kantor';

    protected $fillable = [
        'nama_kantor',
        'jenis',
        'alamat',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function berkasKredit()
    {
        return $this->hasMany(BerkasKredit::class);
    }
}