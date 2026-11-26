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

    /**
     * Generate Rapor Kompetensi PDF dan simpan ke storage.
     *
     * @return array{path: string, data: array}
     */
    public function generateCompetencyReport(Student $student, AcademicYear $academicYear): array
    {
        $data = $this->buildCompetencyReportData($student, $academicYear);

        $pdf = Pdf::loadView('pdf.rapor-kompetensi', $data)
            ->setPaper('a4', 'portrait');

        $path = $this->competencyPath($student, $academicYear, 'pdf');

        Storage::disk('public')->put($path, $pdf->output());

        return [
            'path' => $path,
            'data' => $data,
        ];
    }

    /**
     * Generate Rapor Kompetensi versi HTML lalu simpan sebagai file Word (.doc).
     *
     * @return array{path: string, html: string, data: array}
     */
    public function generateCompetencyReportHtml(Student $student, AcademicYear $academicYear): array
    {
        $data = $this->buildCompetencyReportData($student, $academicYear);

        $html = view('word.rapor-kompetensi', $data)->render();

        $path = $this->competencyPath($student, $academicYear, 'word');

        Storage::disk('public')->put($path, $html);

        return [
            'path' => $path,
            'html' => $html,
            'data' => $data,
        ];
    }

    /**
     * Path penyimpanan file rapor kompetensi.
     */
    protected function competencyPath(Student $student, AcademicYear $academicYear, string $format): string
    {
        $extension = $format === 'word' ? 'doc' : 'pdf';

        return "reports/competency/{$format}/rapor-kompetensi-{$student->id}-{$academicYear->id}.{$extension}";
    }

    /**
     * Kumpulkan seluruh data yang dibutuhkan template rapor kompetensi.
     */
    protected function buildCompetencyReportData(Student $student, AcademicYear $academicYear): array
    {
        $student->loadMissing(['classRoom', 'classRoom.teacher']);

        $school = SchoolProfile::first();

        $competencyGrades = CompetencyGrade::with(['indicator', 'indicator.domain'])
            ->where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->get();

        // Kelompokkan per domain
        $gradesByDomain = $competencyGrades->groupBy(fn ($grade) => $grade->indicator->domain->name);

        // Urutkan domain berdasarkan sort_order
        $sortedDomains = CompetencyDomain::with('indicators')
            ->whereHas('indicators', fn ($q) => $q->whereIn('id', $competencyGrades->pluck('indicator_id')))
            ->orderBy('sort_order')
            ->get();

        $isDraft = $competencyGrades->where('is_submitted', false)->count() > 0
            || $competencyGrades->isEmpty();

        return [
            'student' => $student,
            'academicYear' => $academicYear,
            'school' => $school,
            'gradesByDomain' => $gradesByDomain,
            'sortedDomains' => $sortedDomains,
            'isDraft' => $isDraft,
            'printDate' => now()->locale('id')->translatedFormat('d F Y'),
        ];
    }
}
