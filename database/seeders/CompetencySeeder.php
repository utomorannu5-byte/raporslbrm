<?php

namespace Database\Seeders;

use App\Models\CompetencyDomain;
use App\Models\CompetencyIndicator;
use Illuminate\Database\Seeder;

class CompetencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domains = [
            // Berbahasa
            ['name' => 'Berbahasa', 'indicators' => ['Menyimak/mendengarkan', 'Berbicara', 'Membaca', 'Menulis']],
            // Berhitung
            ['name' => 'Berhitung', 'indicators' => ['Keterampilan Dasar', 'Operasi Hitung', 'Pemecahan Masalah', 'Numerasi']],
            // Pendidikan Agama Islam
            ['name' => 'Pendidikan Agama Islam', 'indicators' => ['Al-Qur\'an/Iqro', 'Akidah', 'Akhlak']],
            // Binadiri
            ['name' => 'Binadiri', 'indicators' => ['Kebersihan', 'Kerapian', 'Makan', 'Toilet Training']],
            // Perilaku
            ['name' => 'Perilaku', 'indicators' => ['Sabar', 'Ingin tahu', 'Percaya diri', 'Taat aturan', 'Peduli', 'Tanggung jawab']],
            // Sosialisasi
            ['name' => 'Sosialisasi', 'indicators' => ['Empati', 'Keterampilan Interpersonal', 'Regulasi Emosi', 'Pengetahuan Sosial']],
            // Memasak
            ['name' => 'Memasak', 'indicators' => ['Menggoreng', 'Merebus', 'Menyeduh', 'Memotong/Menggunting']],
            // Sabun Cair
            ['name' => 'Sabun Cair', 'indicators' => ['Persiapan alat', 'Proses pembuatan', 'Packing', 'Perawatan alat']],
        ];

        foreach ($domains as $domainData) {
            $domain = CompetencyDomain::create([
                'name' => $domainData['name'],
                'sort_order' => 0,
            ]);

            foreach ($domainData['indicators'] as $index => $indicatorName) {
                CompetencyIndicator::create([
                    'domain_id' => $domain->id,
                    'name' => $indicatorName,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
