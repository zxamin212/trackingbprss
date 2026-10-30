<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKredit extends Model
{
    protected $table = 'jenis_kredit';

    protected $fillable = ['nama', 'aktif', 'urutan'];

    protected $casts = ['aktif' => 'boolean'];

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeUrut($query)
    {
        return $query->orderBy('urutan')->orderBy('nama');
    }

    /** Nama jenis kredit yang aktif, sesuai urutan. */
    public static function namaAktif(): array
    {
        return static::aktif()->urut()->pluck('nama')->all();
    }
}