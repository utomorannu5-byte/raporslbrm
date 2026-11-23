<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolProfile;

class SchoolProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SchoolProfile::create([
            'name' => 'SEKOLAH LUAR BIASA RUMAH MELATI',
            'address' => 'Telaga Mas Blok J1 No. 3 Rt. 001/014 Kel. Harapan Baru Kec. Bekasi Utara',
            'phone' => '0821 1234 3787, 0812 7408 9081',
            'email' => 'yppabki_rumahmelati@ymail.com',
            'logo_path' => null,
        ]);
    }
}