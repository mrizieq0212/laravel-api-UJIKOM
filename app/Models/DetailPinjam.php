<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPinjam extends Model
{
    use HasFactory;

    protected $table = 'detail_pinjam'; // sesuaikan dengan nama tabel di database kamu

    protected $fillable = [
        'peminjaman_id',
        'alat_id',
        'jumlah',
    ];

    // Relasi ke Peminjaman
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    // Relasi ke Alat
    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }
}