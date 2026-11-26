<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor Kompetensi - {{ $student->name }}</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }

        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .watermark .draft {
            width: 100%;
            height: 100%;
            background: red;
            opacity: 0.15;
        }

        .watermark .draft text {
            fill: #dc2626;
            font-size: 120px;
            font-weight: bold;
            text-anchor: middle;
            dominant-baseline: middle;
            transform: rotate(-45deg);
        }

        .content {
            position: relative;
            z-index: 1;
        }

        table {
            border-collapse: collapse;
        }

        th, td {
            padding: 4px 6px;
            font-size: 11px;
            vertical-align: middle;
        }

        th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
        }

        .center { text-align: center; }
        .left { text-align: left; }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin: 14px 0 8px;
        }

        .kop {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .kop td {
            vertical-align: middle;
        }

        .kop-logo {
            width: 90px;
            text-align: center;
        }

        .kop-text {
            text-align: center;
        }

        .school-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .school-address {
            font-size: 10px;
            margin-top: 2px;
        }

        .school-contact {
            font-size: 10px;
            margin-top: 1px;
        }

        .judul {
            text-align: center;
            margin: 14px 0 16px;
        }

        .judul-utama {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .judul-sub {
            font-size: 12px;
            font-weight: bold;
            margin-top: 3px;
        }

        .identitas {
            width: 100%;
            margin-bottom: 14px;
        }

        .identitas td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 11px;
        }

        .identitas .label {
            width: 150px;
            font-weight: bold;
        }

        .identitas .titik {
            width: 12px;
            text-align: center;
        }

        .data-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 11px;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
        }

        .empty-text {
            text-align: center;
            font-style: italic;
            color: #555;
        }

        .status-note {
            margin-top: 10px;
            font-size: 9px;
            color: #b91c1c;
            text-align: center;
            font-style: italic;
        }

        @media print {
            @page {
                margin: 20mm 15mm 20mm 15mm;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    @php
        // Siapkan logo sebagai base64 agar dompdf bisa membacanya.
        $logoSrc = null;
        if ($school && $school->logo_path) {
            $logoAbsolute = storage_path('app/public/' . $school->logo_path);
            if (is_file($logoAbsolute)) {
                $logoSrc = 'data:' . mime_content_type($logoAbsolute) . ';base64,' . base64_encode(file_get_contents($logoAbsolute));
            }
        }

        // Watermark DRAFT
        $watermarkSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="595" height="842">'
            . '<text x="297" y="435" text-anchor="middle" dominant-baseline="middle"'
            . ' transform="rotate(-45 297 421)" fill="#dc2626" fill-opacity="0.15"'
            . ' font-family="Arial, Helvetica, sans-serif" font-size="130" font-weight="bold">DRAFT</text>'
            . '</svg>';
        $watermarkSrc = 'data:image/svg+xml;base64,' . base64_encode($watermarkSvg);
    @endphp

    @if ($isDraft)
        <div class="watermark">
            <img src="{{ $watermarkSrc }}" alt="DRAFT">
        </div>
    @endif

    <div class="content">
        {{-- ================= KOP SURAT ================= --}}
        <table class="kop">
            <tr>
                @if ($logoSrc)
                    <td class="kop-logo">
                        <img src="{{ $logoSrc }}" alt="Logo Sekolah">
                    </td>
                @endif
                <td class="kop-text">
                    <div class="school-name">{{ $school->name ?? 'SEKOLAH LUAR BIASA RUMAH MELATI' }}</div>
                    <div class="school-address">{{ $school->address ?? '-' }}</div>
                    <div class="school-contact">
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
        <div class="judul">
            <div class="judul-utama">Laporan Hasil Belajar Kompetensi</div>
            <div class="judul-sub">Rapor Kompetensi</div>
            <div class="judul-sub">Tahun Ajaran {{ $academicYear->year }} - Semester {{ $academicYear->semester }}</div>
        </div>

        {{-- ================= IDENTITAS SISWA ================= --}}
        <table class="identitas">
            <tr>
                <td class="label">Nama Siswa</td>
                <td class="titik">:</td>
                <td>{{ $student->name }}</td>
            </tr>
            <tr>
                <td class="label">NISN</td>
                <td class="titik">:</td>
                <td>{{ $student->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kelas</td>
                <td class="titik">:</td>
                <td>{{ $student->classRoom->name ?? '-' }}</td>
            </tr>
        </table>

        {{-- ================= TABLE KOMPETENSI PER DOMAIN ================= --}}
        <div class="section-title">Nilai Kompetensi</div>

        {{-- Jika belum ada data kompetensi --}}
        @if ($sortedDomains->isEmpty())
            <div class="empty-text">Belum ada indikator kompetensi yang di-assign ke kelas ini.<br>Hubungi admin untuk mengatur indikator pada menu <strong>Kelas → Edit</strong>.</div>
        @else
            {{-- Tampilkan per domain --}}
            {{-- Header tabel global --}}
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="center" style="width: 40px;">No</th>
                        <th style="width: 35%;">Indikator Kompetensi</th>
                        <th class="center" style="width: 15%;">M</th>
                        <th class="center" style="width: 15%;">AV</th>
                        <th class="center" style="width: 15%;">AF</th>
                        <th class="center" style="width: 15%;">AP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sortedDomains as $domain)
                        {{-- Baris header domain (nama domain)--}
                        <tr>
                            <td colspan="6" style="background-color: #e8e8e8; font-weight: bold; text-align: center; padding: 6px;">
                                {{ $domain->name }}
                            </td>
                        </tr>

                        {{-- Tampilkan indikator di dalam domain ini --}}
                        @foreach ($domain->indicators as $indicator)
                            @php
                                $grade = $gradesByDomain->get($indicator->id)->first();
                                $value = $grade ? $grade->value : '';
                            @endphp>

                            {{-- Baris indikator dengan centang --}
                            <tr>
                                <td class="center">{{ $loop->iteration }}</td>
                                <td style="font-weight: bold;">{{ $indicator->name }}</td>
                                <td class="center">{{ $value === 'M' ? '✓' : '' }}</td>
                                <td class="center">{{ $value === 'AV' ? '✓' : '' }}</td>
                                <td class="center">{{ $value === 'AF' ? '✓' : '' }}</td>
                                <td class="center">{{ $value === 'AP' ? '✓' : '' }}</td>
                            </tr>
                        @endforeach
                        {{-- Tambahkan baris kosong jika domain memiliki indikator tapi belum semua terisi --}}
                        @if ($gradesByDomain->has($domain->id))
                            @foreach ($domain->indicators as $indicator)
                                @php
                                    $grade = $gradesByDomain->get($indicator->id)->first();
                                    $value = $grade ? $grade->value : '';
                                @endphp
                                @if !$loop->parent->outer->hasLoopInfo || $loop->index < count($domain->indicators) - 1
                                @endif
                            @endforeach
                        @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        {{-- Penjelasan keterangan --}}
        <div style="margin: 14px 0 8px; font-size: 9px;">
            <strong>Keterangan:</strong><br>
            M = Mandiri&nbsp;&nbsp;|&nbsp;&nbsp;AV = Dengan Bantuan Verbal<br>
            AF = Dengan Bantuan Fisik&nbsp;&nbsp;|&nbsp;&nbsp;AP = Dengan Bantuan Penuh
        </div>
        @endif

        {{-- ================= TANDA TANGAN ================= --}}
        <div style="margin-top: 26px; font-size: 11px;">
            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                <div>
                    <strong>Wali Kelas,</strong><br>
                    <div style="height: 40px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $teacher->name ?? '............................' }}</div>
                </div>
                <div>
                    <strong>Orang Tua / Wali,</strong><br>
                    <div style="height: 40px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $student->parent_name ?? '............................' }}</div>
                </div>
            </div>
        </div>

        <div class="status-note">@if ($isDraft) Dokumen ini masih berstatus DRAFT dan belum final. @endif</div>
    </div>
</body>
</html>