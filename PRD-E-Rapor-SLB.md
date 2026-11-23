PRODUCT REQUIREMENTS DOCUMENT (PRD) FINAL
Nama Produk: E-Rapor SLB "Rumah Melati"
Versi: 3.0 (Final)
Tanggal: 23 November 2026
Status: Approved & Ready for Development

1. Ringkasan Eksekutif
E-Rapor SLB "Rumah Melati" adalah aplikasi berbasis web yang dirancang untuk mengakomodasi kebutuhan pelaporan hasil belajar siswa berkebutuhan khusus. Berbeda dengan rapor sekolah reguler, sistem ini menghasilkan dua dokumen output utama untuk setiap siswa di semua jenjang (SD, SMP, SMA, Lanjutan, Vokasional): Rapor Akademik (berbasis nilai angka/huruf) dan Rapor Kompetensi (berbasis checklist kemandirian M/AV/AF/AP). Aplikasi ini berfokus pada efisiensi input guru, fleksibilitas master data, dan pengarsipan digital.

2. Tujuan Produk
Menyediakan sistem input nilai terpusat dengan dua antarmuka (Tab Akademik & Tab Kompetensi) dalam satu halaman siswa.

Mengotomatiskan konversi nilai angka menjadi huruf (ejaan teks).

Memfasilitasi pengarsipan rapor dalam format PDF untuk mencegah penumpukan data histori yang memberatkan sistem.

3. Arsitektur Peran & Hak Akses (Role-Based Access Control)
Aplikasi menggunakan satu pintu login (Single Sign-On). Sistem akan mendeteksi role dan menampilkan menu yang sesuai.

A. Admin / Operator Sekolah
Fokus: Konfigurasi sistem, master data, dan manajemen pengguna.

Akses Sidebar: Dashboard, Pengguna, Siswa & Kelas, Tahun Ajaran, Master Mapel, Master Kompetensi, Konversi Nilai, Pengaturan Sekolah, Arsip Rapor (Global).

B. Guru / Wali Kelas
Fokus: Input nilai, submit, cetak rapor, dan melihat arsip miliknya.

Akses Sidebar: Dashboard, Input Nilai, Cetak Rapor, Arsip Rapor.

4. Alur Kerja Utama (Business Flow)
4.1 Siklus Tahun Ajaran & Copy Data
Admin membuat Tahun Ajaran baru (misal: 2025/2026 Genap).

Admin menekan tombol "Copy Data".

Sistem menduplikasi Master Data (Kelas, Siswa, Mapel, Indikator Kompetensi) dari semester sebelumnya ke semester baru.

PENTING: Semua data nilai (Value) dari semester sebelumnya dikosongkan (reset). Guru harus menginput ulang nilai di semester baru.

4.2 Alur Input Nilai (Guru)
Guru login -> Pilih menu Input Nilai -> Pilih Kelas -> Pilih Siswa.

Muncul halaman profil siswa dengan 2 Tab:

Tab 1 (Akademik): Guru mengisi KKM, Nilai Angka (sistem otomatis mengonversi ke Huruf), Nilai Kepribadian (A/B/C), dan Absensi (Sakit/Izin/Alpa). Kolom deskripsi/catatan tidak disediakan.

Tab 2 (Kompetensi): Guru mencentang salah satu dari 4 opsi (M, AV, AF, AP) untuk setiap indikator kompetensi. Kolom deskripsi/catatan tidak disediakan.

Guru bebas berpindah tab. Data tersimpan sebagai Draft.

Setelah selesai, Guru menekan tombol [SUBMIT & KUNCI NILAI]. Akan muncul pop-up konfirmasi. Jika disetujui, nilai terkunci (read-only).

4.3 Alur Cetak & Arsip
Setelah nilai di-submit (atau masih draft), Guru dapat menekan tombol Cetak Rapor Akademik atau Cetak Rapor Kompetensi.

Sistem menghasilkan file PDF sesuai format sekolah.

File PDF otomatis tersimpan di menu Arsip Rapor (berdasarkan nama siswa, tipe rapor, dan tanggal cetak).

Tidak ada fitur cetak massal (1 PDF untuk 1 kelas). Cetak dilakukan per siswa per dokumen.

5. Spesifikasi Master Data (Fleksibel)
Semua master data dapat dikelola (CRUD) oleh Admin melalui Sidebar.

Master Sekolah: Nama Sekolah, Alamat, Telepon, Email, Logo (Upload).

Master Pengguna: Nama Guru, Username, Password, Role.

Master Kelas: Nama Kelas (Lanjutan I, Vokasional, SMPLB VIII), Wali Kelas.

Master Siswa: NISN, Nama Siswa, Kelas, Nama Orang Tua (untuk tanda tangan).

Master Mapel Akademik: Nama Mapel, KKM Default.

Master Kompetensi:

Domain: Berbahasa, Memasak, dll.

Indikator: Menyimak, Merebus, dll (terhubung ke Domain).

Master Konversi Nilai: Pengaturan rentang angka ke teks. Admin dapat mengedit format pengejaan manual jika ada pengecualian.

Assign Data ke Kelas: Mapel dan Indikator Kompetensi di-assign (dipilih) per kelas agar tampilan di form input guru tidak penuh dengan data yang tidak relevan.

6. Spesifikasi UI/UX (Tampilan)
Dashboard Admin: Kartu statistik (Total Siswa, Total Guru, Total Kelas). Status kelengkapan submit per kelas.

Dashboard Guru: Kartu statistik (Total Siswa Kelasnya, Sudah Submit, Belum Submit). Progress bar kelengkapan nilai.

Sidebar Menu: Menggunakan gaya menu list. Master data diletakkan sejajar di sidebar (khusus Admin).

Halaman Input Nilai: Menggunakan model Tab (seperti poin 4.2).

Halaman Arsip: Berupa tabel dengan filter (Kelas, Tahun Ajaran) dan tombol unduh (ikon PDF).

7. Struktur Database (ERD Ringkas untuk Programmer)
users (id, name, email, password, role)

school_profiles (id, name, address, phone, email, logo_path)

academic_years (id, year, semester, is_active)

classes (id, name, teacher_id, academic_year_id)

students (id, class_id, nisn, name, parent_name)

subjects (id, name, default_kkm)

competency_domains (id, name)

competency_indicators (id, domain_id, name)

class_subjects (class_id, subject_id) -> Pivot Assign Mapel

class_indicators (class_id, indicator_id) -> Pivot Assign Kompetensi

grade_conversions (id, min_score, max_score, text_label)

academic_grades (id, student_id, academic_year_id, subject_id, kkm, score_number, score_text, is_submitted)

student_attitudes (id, student_id, academic_year_id, sikap, kerajinan, kebersihan, sick, permission, absent)

competency_grades (id, student_id, academic_year_id, indicator_id, value [M/AV/AF/AP], is_submitted)

report_archives (id, student_id, academic_year_id, type [academic/competency], file_path, created_at)

8. Kebutuhan Non-Fungsional
Teknologi: Rekomendasi menggunakan Laravel/Node.js (Backend) + React/Vue (Frontend) + MySQL/PostgreSQL (Database).

PDF Generator: Menggunakan library seperti DomPDF (PHP) atau Puppeteer (Node.js) untuk hasil cetak yang presisi sesuai kop surat.

Keamanan: Password di-hash (bcrypt). Fitur Submit & Kunci mencegah manipulasi nilai setelah dicetak.

Responsif: Minimal dapat diakses dengan baik di layar Desktop/Laptop (karena tabel nilai cukup lebar).

9. Risiko & Mitigasi
Risiko: Guru lupa menekan tombol "Submit" sehingga nilai masih berstatus draft padahal rapor sudah dicetak.

Mitigasi: Berikan watermark "DRAFT" berwarna merah pada PDF jika status nilai belum di-submit.

Risiko: Kesalahan input nilai angka yang menyebabkan konversi huruf tidak sesuai.

Mitigasi: Pastikan form input nilai angka dibatasi (0-100) dan tampilkan preview teks huruf secara real-time saat mengetik.