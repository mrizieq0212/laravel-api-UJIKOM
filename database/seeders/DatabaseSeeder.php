<?php

namespace Database\Seeders;

use App\Models\Alat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KategoriSeeder::class,
            AlatSeeder::class,
            PeminjamanSeeder::class,
            DetailPinjamSeeder::class,
            PengembalianSeeder::class,
            LogAktivitasSeeder::class,
            // ProdukSeeder::class, // jika memang digunakan
        ]);
    }
}