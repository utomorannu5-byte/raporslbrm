<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor Akademik - {{ $student->name }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #000;
        }

        .watermark {
            position: absolute;
            top: 380px;
            left: 120px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 120px;
            font-weight: bold;
            color: #dc2626;
            opacity: 0.15;
            transform: rotate(-45deg);
            transform-origin: center center;
            z-index: 0;
            white-space: nowrap;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        table {
            border-collapse: collapse;
        }

        /* ---------- Kop Surat ---------- */
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

        .kop-logo img {
            width: 75px;
            height: 75px;
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

        /* ---------- Judul ---------- */
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

        /* ---------- Identitas ---------- */
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

        /* ---------- Judul Bagian ---------- */
        .section-title {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin: 14px 0 8px;
        }

        /* ---------- Tabel data ---------- */
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

        .data-table td.center {
            text-align: center;
        }

        .data-table td.left {
            text-align: left;
        }

        .col-no {
            width: 34px;
        }

        .col-kkm {
            width: 50px;
        }

        .col-angka {
            width: 75px;
        }

        .col-huruf {
            width: 75px;
        }

        .col-jumlah {
            width: 90px;
        }

        .empty-row td {
            text-align: center;
            font-style: italic;
            color: #555;
        }

        /* ---------- Tanda Tangan ---------- */
        .ttd {
            width: 100%;
            margin-top: 26px;
        }

        .ttd td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 11px;
        }

        .ttd-label {
            font-weight: normal;
        }

        .ttd-space {
            height: 55px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }

        .ttd-ket {
            font-size: 10px;
            margin-top: 2px;
        }

        /* ---------- Footer ---------- */
        .footer {
            margin-top: 18px;
            text-align: right;
            font-size: 11px;
        }

        .status-note {
            margin-top: 10px;
            font-size: 9px;
            color: #b91c1c;
            text-align: center;
            font-style: italic;
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
    @endphp

    @if ($isDraft)
        <div class="watermark">DRAFT</div>
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
            <div class="judul-utama">Laporan Hasil Belajar Siswa</div>
            <div class="judul-sub">Rapor Akademik</div>
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
            <tr>
                <td class="label">Nama Orang Tua / Wali</td>
                <td class="titik">:</td>
                <td>{{ $student->parent_name ?? '-' }}</td>
            </tr>
        </table>

        {{-- ================= NILAI AKADEMIK ================= --}}
        <div class="section-title">Nilai Akademik</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Mata Pelajaran</th>
                    <th class="col-kkm">KKM</th>
                    <th class="col-angka">Nilai Angka</th>
                    <th class="col-huruf">Nilai Huruf</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grades as $index => $grade)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="left">{{ $grade->subject->name ?? '-' }}</td>
                        <td class="center">{{ $grade->kkm ?? '-' }}</td>
                        <td class="center">{{ $grade->score_number ?? '-' }}</td>
                        <td class="center">{{ $grade->score_text ?? '-' }}</td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="5">Belum ada nilai akademik.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ================= KEPRIBADIAN ================= --}}
        <div class="section-title">Kepribadian</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Aspek</th>
                    <th class="col-huruf">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="left">Sikap</td>
                    <td class="center">{{ $attitude->sikap ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="left">Kerajinan</td>
                    <td class="center">{{ $attitude->kerajinan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="left">Kebersihan</td>
                    <td class="center">{{ $attitude->kebersihan ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ================= ABSENSI ================= --}}
        <div class="section-title">Absensi</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="col-jumlah">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="left">Sakit</td>
                    <td class="center">{{ $attitude->sick ?? 0 }}</td>
                </tr>
                <tr>
                    <td class="left">Izin</td>
                    <td class="center">{{ $attitude->permission ?? 0 }}</td>
                </tr>
                <tr>
                    <td class="left">Tanpa Keterangan</td>
                    <td class="center">{{ $attitude->absent ?? 0 }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ================= TANDA TANGAN ================= --}}
        <table class="ttd">
            <tr>
                <td>
                    <div class="ttd-label">Wali Kelas,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-nama">{{ $teacher->name ?? '............................' }}</div>
                </td>
                <td>
                    <div class="ttd-label">Orang Tua / Wali,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-nama">{{ $student->parent_name ?? '............................' }}</div>
                </td>
            </tr>
        </table>

        <div class="footer">Bekasi, {{ $printDate }}</div>

        @if ($isDraft)
            <div class="status-note">Dokumen ini masih berstatus DRAFT dan belum final.</div>
        @endif
    </div>
</body>
</html>
