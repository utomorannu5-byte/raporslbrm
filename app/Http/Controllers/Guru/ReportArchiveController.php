<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ReportArchive;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ReportArchiveController extends Controller
{
    /**
     * Display a listing of the report archives for the logged-in teacher's class.
     */
    public function index(): View
    {
        $teacher = auth()->user();

        $archives = ReportArchive::with(['student', 'academicYear'])
            ->whereHas('student.classRoom', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('guru.report-archives.index', compact('archives'));
    }

    /**
     * Download a single report archive.
     */
    public function download($id): Response
    {
        $archive = ReportArchive::whereHas('student.classRoom', function ($query) {
            $query->where('teacher_id', auth()->id());
        })->findOrFail($id);

        return response()->download(
            storage_path('app/public/' . $archive->file_path),
            basename($archive->file_path)
        );
    }
}