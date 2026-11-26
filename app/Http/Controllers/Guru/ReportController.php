<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ReportArchive;
use App\Models\Student;
use App\Services\PdfGeneratorService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        protected PdfGeneratorService $pdfGenerator
    ) {}

    /**
     * Cetak Rapor Akademik dalam format PDF.
     */
    public function academic(Student $student): BinaryFileResponse
    {
        $this->ensureClassTeacher($student);

        $academicYear = $this->activeAcademicYear();

        $result = $this->pdfGenerator->generateAcademicReport($student, $academicYear);

        ReportArchive::create([
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'type' => 'academic',
            'format' => 'pdf',
            'file_path' => $result['path'],
        ]);

        $filename = 'Rapor-Akademik-'.Str::slug($student->name).'-'.Str::slug($academicYear->year).'.pdf';

        return response()->download(
            Storage::disk('public')->path($result['path']),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Unduh Rapor Akademik dalam format Word (.doc).
     */
    public function academicWord(Student $student): BinaryFileResponse
    {
        $this->ensureClassTeacher($student);

        $academicYear = $this->activeAcademicYear();

        $result = $this->pdfGenerator->generateAcademicReportHtml($student, $academicYear);

        ReportArchive::create([
            'student_id' => $student->id,
            'academic_year_id' => $academicYear->id,
            'type' => 'academic',
            'format' => 'word',
            'file_path' => $result['path'],
        ]);

        $filename = 'Rapor-Akademik-'.Str::slug($student->name).'-'.Str::slug($academicYear->year).'.doc';

        return response()->download(
            Storage::disk('public')->path($result['path']),
            $filename,
            ['Content-Type' => 'application/msword']
        );
    }

    /**
     * Pastikan hanya wali kelas dari siswa tersebut yang boleh mengakses.
     */
    protected function ensureClassTeacher(Student $student): void
    {
        $student->loadMissing('classRoom');

        if (! $student->classRoom || $student->classRoom->teacher_id !== auth()->id()) {
            abort(403, 'Anda tidak berwenang mengakses siswa ini.');
        }
    }

    /**
     * Ambil tahun ajaran aktif atau gagal dengan 404.
     */
    protected function activeAcademicYear(): AcademicYear
    {
        $academicYear = AcademicYear::where('is_active', true)->first();

        if (! $academicYear) {
            abort(404, 'Tidak ada tahun ajaran aktif.');
        }

        return $academicYear;
    }
}
