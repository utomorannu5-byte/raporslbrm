<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportArchive;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class ReportArchiveController extends Controller
{
    /**
     * Display a listing of the report archives.
     */
    public function index(): View
    {
        $archives = ReportArchive::with(['student', 'academicYear'])
            ->latest('created_at')
            ->paginate(20);

        return view('admin.report-archives.index', compact('archives'));
    }

    /**
     * Download a single report archive.
     */
    public function download($id): \Symfony\Component\HttpFoundation\Response
    {
        $archive = ReportArchive::findOrFail($id);

        return response()->download(
            storage_path('app/public/' . $archive->file_path),
            basename($archive->file_path)
        );
    }

    /**
     * Download all report archives as a ZIP file.
     */
    public function downloadAll(): \Symfony\Component\HttpFoundation\Response
    {
        $archives = ReportArchive::latest('created_at')->get();

        $zip = new \ZipArchive();
        $zipPath = tempnam(sys_get_temp_dir(), 'reports_');

        if ($zip->open($zipPath) !== true) {
            abort(500, 'Gagal membuat file ZIP.');
        }

        foreach ($archives as $archive) {
            $filePath = storage_path('app/public/' . $archive->file_path);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, basename($filePath));
            }
        }

        $zip->close();

        $response = response()->download($zipPath, 'arsip-rapor.zip');
        // Hapus file tmp setelah di-download
        @unlink($zipPath);

        return $response;
    }
}