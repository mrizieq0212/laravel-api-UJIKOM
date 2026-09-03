<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'tgl_pinjam',
        'tgl_kembali_plan',
        'status',
    ];

    // Tambahkan ini supaya tgl_pinjam & tgl_kembali_plan otomatis jadi objek Carbon
    protected $casts = [
        'tgl_pinjam' => 'date',
        'tgl_kembali_plan' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailPinjams()
    {
        return $this->hasMany(DetailPinjam::class);
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class);
    }
}