<!DOCTYPE html>
<html lang="id" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word">
<head>
    <meta charset="UTF-8">
    <title>Rapor Kompetensi - {{ $student->name }}</title>
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
        <div class="judul-utama">Laporan Hasil Belajar Kompetensi</div>
        <div class="judul-sub">Rapor Kompetensi</div>
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
    </table>

    {{-- ================= TABLE KOMPETENSI PER DOMAIN ================= --}}
    <div class="section-title">Nilai Kompetensi</div>
    <table style="width: 100%; margin: 6pt 0 10pt 0;" border="1" cellpadding="4" cellspacing="0">
        <tr>
            <td class="cell-head" style="width: 40pt;">No</td>
            <td class="cell-head" style="width: 35%;">Indikator Kompetensi</td>
            <td class="cell-head" style="width: 15%;">M</td>
            <td class="cell-head" style="width: 15%;">AV</td>
            <td class="cell-head" style="width: 15%;">AF</td>
            <td class="cell-head" style="width: 15%;">AP</td>
        </tr>

        @if ($sortedDomains->isEmpty())
            <tr>
                <td class="cell center" colspan="6"><i>Belum ada indikator kompetensi yang di-assign ke kelas ini.</i></td>
            </tr>
        @else
            {{-- Loop domain --}}
            @foreach ($sortedDomains as $domain)
                {{-- Baris header domain --}
                <tr>
                    <td class="cell-center" colspan="6" style="background-color: #e8e8e8; font-weight: bold; text-align: center; padding: 6px;">
                        {{ $domain->name }}
                    </td>
                </tr>

                {{-- Loop indikator di dalam domain --}}
                @foreach ($domain->indicators as $indicator)
                    {{-- Ambil nilai kompetensi --}
                    @php
                        $grade = $gradesByDomain->get($indicator->id)->first();
                        $value = $grade ? $grade->value : '';
                    @endphp>

                    {{-- Baris indikator dengan centang --}
                    <tr>
                        <td class="cell center">{{ $loop->iteration }}</td>
                        <td style="font-weight: bold;">{{ $indicator->name }}</td>
                        <td class="cell center">{{ $value === 'M' ? '✓' : '' }}</td>
                        <td class="cell center">{{ $value === 'AV' ? '✓' : '' }}</td>
                        <td class="cell center">{{ $value === 'AF' ? '✓' : '' }}</td>
                        <td class="cell center">{{ $value === 'AP' ? '✓' : '' }}</td>
                    </tr>
                @endforeach
            @endforeach

            {{-- Penjelasan keterangan --}}
            <tr>
                <td class="cell-left" colspan="6" style="font-size: 9pt; margin-top: 6pt;">
                    <strong>Keterangan:</strong> M = Mandiri&nbsp;|&nbsp;AV = Dengan Bantuan Verbal<br>
                    AF = Dengan Bantuan Fisik&nbsp;|&nbsp;AP = Dengan Bantuan Penuh
                </td>
            </tr>
        @endif
    </table>

    {{-- ================= TANDA TANGAN ================= --}}
    <table style="width: 100%; margin-top: 20pt;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: top; font-size: 11px;">
                <div>Wali Kelas,</div>
                <div style="height: 45pt;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $teacher->name ?? '............................' }}</div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: top; font-size: 11px;">
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