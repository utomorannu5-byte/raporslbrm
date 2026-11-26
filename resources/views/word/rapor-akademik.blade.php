<!DOCTYPE html>
<html lang="id" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word">
<head>
    <meta charset="UTF-8">
    <title>Rapor Akademik - {{ $student->name }}</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page {
            size: A4 portrait;
            margin: 2cm 2cm 2cm 2cm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.35;
            color: #000000;
        }

        table {
            border-collapse: collapse;
        }

        .school-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .school-meta {
            font-size: 9pt;
        }

        .judul-utama {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .judul-sub {
            font-size: 11pt;
            font-weight: bold;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1pt solid #000000;
        }

        .cell {
            border: 1pt solid #000000;
            padding: 3pt 5pt;
            font-size: 10pt;
        }

        .cell-head {
            border: 1pt solid #000000;
            padding: 3pt 5pt;
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            background-color: #e8e8e8;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .draft-banner {
            border: 2pt solid #dc2626;
            color: #dc2626;
            font-weight: bold;
            text-align: center;
            font-size: 14pt;
            padding: 4pt;
            letter-spacing: 4pt;
        }
    </style>
</head>
<body>
    @php
        $logoSrc = null;
        if ($school && $school->logo_path) {
            $logoAbsolute = storage_path('app/public/' . $school->logo_path);
            if (is_file($logoAbsolute)) {
                $logoSrc = 'data:' . mime_content_type($logoAbsolute) . ';base64,' . base64_encode(file_get_contents($logoAbsolute));
            }
        }
    @endphp

    @if ($isDraft)
        <table style="width: 100%; margin-bottom: 8pt;">
            <tr>
                <td class="draft-banner">DRAFT</td>
            </tr>
        </table>
    @endif

    {{-- ================= KOP SURAT ================= --}}
    <table style="width: 100%; border-bottom: 3pt double #000000; margin-bottom: 10pt;">
        <tr>
            @if ($logoSrc)
                <td style="width: 80pt; text-align: center; vertical-align: middle;">
                    <img src="{{ $logoSrc }}" alt="Logo Sekolah" style="width: 60pt; height: 60pt;">
                </td>
            @endif
            <td style="text-align: center; vertical-align: middle;">
                <div class="school-name">{{ $school->name ?? 'SEKOLAH LUAR BIASA RUMAH MELATI' }}</div>
                <div class="school-meta">{{ $school->address ?? '-' }}</div>
                <div class="school-meta">
                    @if ($school && $school->phone)
                        Telp. {{ $school->phone }}
                    @endif
                    @if ($school && $school->phone && $school->email)
                        &nbsp;|&nbsp;
                    @endif
                    @if ($school && $school->email)
                        Email: {{ $school->email }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- ================= JUDUL ================= --}}
    <div style="text-align: center; margin: 10pt 0 12pt 0;">
        <div class="judul-utama">Laporan Hasil Belajar Siswa</div>
        <div class="judul-sub">Rapor Akademik</div>
        <div class="judul-sub">Tahun Ajaran {{ $academicYear->year }} - Semester {{ $academicYear->semester }}</div>
    </div>

    {{-- ================= IDENTITAS SISWA ================= --}}
    <table style="width: 100%; margin-bottom: 10pt;">
        <tr>
            <td style="width: 140pt; font-weight: bold; padding: 1pt 3pt;">Nama Siswa</td>
            <td style="width: 10pt; padding: 1pt 3pt;">:</td>
            <td style="padding: 1pt 3pt;">{{ $student->name }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 1pt 3pt;">NISN</td>
            <td style="padding: 1pt 3pt;">:</td>
            <td style="padding: 1pt 3pt;">{{ $student->nisn ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 1pt 3pt;">Kelas</td>
            <td style="padding: 1pt 3pt;">:</td>
            <td style="padding: 1pt 3pt;">{{ $student->classRoom->name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; padding: 1pt 3pt;">Nama Orang Tua / Wali</td>
            <td style="padding: 1pt 3pt;">:</td>
            <td style="padding: 1pt 3pt;">{{ $student->parent_name ?? '-' }}</td>
        </tr>
    </table>

    {{-- ================= NILAI AKADEMIK ================= --}}
    <div class="section-title">Nilai Akademik</div>
    <table style="width: 100%; margin: 6pt 0 10pt 0;" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <td class="cell-head" style="width: 30pt;">No</td>
            <td class="cell-head">Mata Pelajaran</td>
            <td class="cell-head" style="width: 40pt;">KKM</td>
            <td class="cell-head" style="width: 60pt;">Nilai Angka</td>
            <td class="cell-head" style="width: 60pt;">Nilai Huruf</td>
        </tr>
        @forelse ($grades as $index => $grade)
            <tr>
                <td class="cell center">{{ $index + 1 }}</td>
                <td class="cell left">{{ $grade->subject->name ?? '-' }}</td>
                <td class="cell center">{{ $grade->kkm ?? '-' }}</td>
                <td class="cell center">{{ $grade->score_number ?? '-' }}</td>
                <td class="cell center">{{ $grade->score_text ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td class="cell center" colspan="5"><i>Belum ada nilai akademik.</i></td>
            </tr>
        @endforelse
    </table>

    {{-- ================= KEPRIBADIAN ================= --}}
    <div class="section-title">Kepribadian</div>
    <table style="width: 100%; margin: 6pt 0 10pt 0;" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <td class="cell-head">Aspek</td>
            <td class="cell-head" style="width: 60pt;">Nilai</td>
        </tr>
        <tr>
            <td class="cell left">Sikap</td>
            <td class="cell center">{{ $attitude->sikap ?? '-' }}</td>
        </tr>
        <tr>
            <td class="cell left">Kerajinan</td>
            <td class="cell center">{{ $attitude->kerajinan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="cell left">Kebersihan</td>
            <td class="cell center">{{ $attitude->kebersihan ?? '-' }}</td>
        </tr>
    </table>

    {{-- ================= ABSENSI ================= --}}
    <div class="section-title">Absensi</div>
    <table style="width: 100%; margin: 6pt 0 10pt 0;" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <td class="cell-head">Keterangan</td>
            <td class="cell-head" style="width: 70pt;">Jumlah</td>
        </tr>
        <tr>
            <td class="cell left">Sakit</td>
            <td class="cell center">{{ $attitude->sick ?? 0 }}</td>
        </tr>
        <tr>
            <td class="cell left">Izin</td>
            <td class="cell center">{{ $attitude->permission ?? 0 }}</td>
        </tr>
        <tr>
            <td class="cell left">Tanpa Keterangan</td>
            <td class="cell center">{{ $attitude->absent ?? 0 }}</td>
        </tr>
    </table>

    {{-- ================= TANDA TANGAN ================= --}}
    <table style="width: 100%; margin-top: 20pt;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                <div>Wali Kelas,</div>
                <div style="height: 45pt;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $teacher->name ?? '............................' }}</div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                <div>Orang Tua / Wali,</div>
                <div style="height: 45pt;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $student->parent_name ?? '............................' }}</div>
            </td>
        </tr>
    </table>

    <div style="text-align: right; margin-top: 14pt;">Bekasi, {{ $printDate }}</div>

    @if ($isDraft)
        <div style="text-align: center; margin-top: 8pt; font-size: 9pt; font-style: italic; color: #b91c1c;">
            Dokumen ini masih berstatus DRAFT dan belum final.
        </div>
    @endif
</body>
</html>
