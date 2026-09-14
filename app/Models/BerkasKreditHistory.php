<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BerkasKreditHistory extends Model
{
    use HasFactory;

    protected $table = 'berkas_kredit_histories';

    protected $fillable = [
        'berkas_kredit_id', 'status', 'keterangan', 'user_id',
    ];

    public function berkasKredit()
    {
        return $this->belongsTo(BerkasKredit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
