<?php

namespace App\Services;

use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\CompetencyGrade;
use App\Models\Student;
use App\Models\StudentAttitude;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class AcademicYearCopyService
{
    /**
     * Copy master data from one academic year to another.
     *
     * @param  AcademicYear  $from  Tahun ajaran asal (sumber data)
     * @param  AcademicYear  $to    Tahun ajaran tujuan (target)
     * @return array ['classes_copied' => int, 'students_copied' => int]
     *
     * @throws \Exception Jika validasi gagal atau terjadi error saat copy
     */
    public function copy(AcademicYear $from, AcademicYear $to): array
    {
        if ($from->id === $to->id) {
            throw new \Exception('Tahun ajaran asal dan tujuan tidak boleh sama.');
        }

        // Cek apakah tahun ajaran tujuan sudah memiliki kelas
        $existingClasses = ClassRoom::where('academic_year_id', $to->id)->count();
        if ($existingClasses > 0) {
            throw new \Exception('Tahun ajaran tujuan sudah memiliki kelas. Hapus kelas terlebih dahulu jika ingin copy ulang.');
        }

        $classesCopied = 0;
        $studentsCopied = 0;

        DB::transaction(function () use ($from, $to, &$classesCopied, &$studentsCopied) {
            // Ambil semua kelas dari tahun ajaran asal
            $sourceClasses = ClassRoom::with(['subjects', 'competencyIndicators'])
                ->where('academic_year_id', $from->id)
                ->get();

            if ($sourceClasses->isEmpty()) {
                throw new \Exception('Tahun ajaran asal tidak memiliki kelas untuk di-copy.');
            }

            foreach ($sourceClasses as $sourceClass) {
                // Buat kelas baru di tahun ajaran tujuan
                $newClass = ClassRoom::create([
                    'name' => $sourceClass->name,
                    'academic_year_id' => $to->id,
                    'teacher_id' => $sourceClass->teacher_id,
                ]);
                $classesCopied++;

                // Copy pivot class_subjects
                if ($sourceClass->subjects->isNotEmpty()) {
                    $newClass->subjects()->sync($sourceClass->subjects->pluck('id'));
                }

                // Copy pivot class_indicators
                if ($sourceClass->competencyIndicators->isNotEmpty()) {
                    $newClass->competencyIndicators()->sync($sourceClass->competencyIndicators->pluck('id'));
                }

                // Copy siswa dari kelas lama ke kelas baru
                $sourceStudents = Student::where('class_id', $sourceClass->id)->get();

                foreach ($sourceStudents as $sourceStudent) {
                    Student::create([
                        'class_id' => $newClass->id,
                        'nisn' => $this->generateUniqueNisn(),
                        'name' => $sourceStudent->name,
                        'parent_name' => $sourceStudent->parent_name,
                    ]);
                    $studentsCopied++;
                }
            }

            // JANGAN copy nilai (academic_grades, competency_grades, student_attitudes)
            // Semua nilai di tahun ajaran tujuan akan kosong (belum diisi)
        });

        return [
            'classes_copied' => $classesCopied,
            'students_copied' => $studentsCopied,
        ];
    }

    /**
     * Generate a unique NISN for copied students.
     */
    protected function generateUniqueNisn(): string
    {
        do {
            $nisn = '99' . now()->format('ymdHis') . random_int(100, 999);
        } while (Student::where('nisn', $nisn)->exists());

        return $nisn;
    }
}