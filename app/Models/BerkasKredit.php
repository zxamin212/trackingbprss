<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasKredit extends Model
{
    use HasFactory;

    protected $table = 'berkas_kredit';

    protected $fillable = [
        'nomor_berkas', 'nama_nasabah', 'jenis_kredit',
        'status_terkini', 'tanggal_masuk', 'tanggal_selesai',
        'kantor_id', 'user_id',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function kantor()
    {
        return $this->belongsTo(Kantor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function histories()
    {
        return $this->hasMany(BerkasKreditHistory::class)->orderBy('created_at');
    }
}