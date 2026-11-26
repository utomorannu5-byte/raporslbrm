<?php

namespace App\Services;

use App\Models\AcademicGrade;
use App\Models\AcademicYear;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\StudentAttitude;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfGeneratorService
{
    /**
     * Generate Rapor Akademik PDF dan simpan ke storage.
     *
     * @return array{path: string, data: array}
     */
    public function generateAcademicReport(Student $student, AcademicYear $academicYear): array
    {
        $data = $this->buildAcademicReportData($student, $academicYear);

        $pdf = Pdf::loadView('pdf.rapor-akademik', $data)
            ->setPaper('a4', 'portrait');

        $path = $this->academicPath($student, $academicYear, 'pdf');

        Storage::disk('public')->put($path, $pdf->output());

        return [
            'path' => $path,
            'data' => $data,
        ];
    }

    /**
     * Generate Rapor Akademik versi HTML lalu simpan sebagai file Word (.doc).
     *
     * @return array{path: string, html: string, data: array}
     */
    public function generateAcademicReportHtml(Student $student, AcademicYear $academicYear): array
    {
        $data = $this->buildAcademicReportData($student, $academicYear);

        $html = view('word.rapor-akademik', $data)->render();

        $path = $this->academicPath($student, $academicYear, 'word');

        Storage::disk('public')->put($path, $html);

        return [
            'path' => $path,
            'html' => $html,
            'data' => $data,
        ];
    }

    /**
     * Kumpulkan seluruh data yang dibutuhkan template rapor akademik.
     */
    protected function buildAcademicReportData(Student $student, AcademicYear $academicYear): array
    {
        $student->loadMissing(['classRoom.teacher']);

        $school = SchoolProfile::first();

        $grades = AcademicGrade::with('subject')
            ->where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->orderBy('subject_id')
            ->get();

        $attitude = StudentAttitude::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->first();

        // DRAFT jika ada nilai yang belum di-submit, belum ada nilai sama sekali,
        // atau data kepribadian/absensi belum lengkap.
        $isDraft = $grades->isEmpty()
            || $grades->contains(fn (AcademicGrade $grade) => ! $grade->is_submitted)
            || $attitude === null
            || blank($attitude->sikap)
            || blank($attitude->kerajinan)
            || blank($attitude->kebersihan);

        return [
            'student' => $student,
            'academicYear' => $academicYear,
            'school' => $school,
            'grades' => $grades,
            'attitude' => $attitude,
            'teacher' => $student->classRoom->teacher ?? null,
            'isDraft' => $isDraft,
            'printDate' => now()->locale('id')->translatedFormat('d F Y'),
        ];
    }

    /**
     * Path penyimpanan file rapor akademik.
     */
    protected function academicPath(Student $student, AcademicYear $academicYear, string $format): string
    {
        $extension = $format === 'word' ? 'doc' : 'pdf';

        return "reports/academic/{$format}/rapor-akademik-{$student->id}-{$academicYear->id}.{$extension}";
    }
}
