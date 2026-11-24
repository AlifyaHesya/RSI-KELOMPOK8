<?php

namespace Database\Seeders;

use App\Models\KodeAkses;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Kode akses registrasi PIC (ganti sebelum dipakai sungguhan!)
        KodeAkses::updateOrCreate(['kode' => 'PIC-2026'], ['role' => 'PIC', 'aktif' => true]);
    }
}
