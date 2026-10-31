<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Module;
use App\Models\Perti;
use App\Models\Prodi;
use App\Models\Requirement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Data demo: admin, perguruan tinggi, program studi, modul LAM INFOKOM 2.1 (Sarjana Terapan),
 * dan butir persyaratan (Dokumen LED PPEPP + Tabel LKPS).
 */
class AccreditationDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────
        User::query()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'              => 'Administrator',
                'password'          => Hash::make('Admin123'),
                'role'              => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        // ── Perguruan Tinggi ───────────────────────────────
        $pertiUser = User::query()->updateOrCreate(
            ['email' => 'ulbi@gmail.com'],
            [
                'name'              => 'Universitas Logistik & Bisnis Internasional',
                'password'          => Hash::make('Ulbi1234'),
                'role'              => UserRole::Perti,
                'email_verified_at' => now(),
            ]
        );

        $pertiProfile = Perti::query()->updateOrCreate(
            ['user_id' => $pertiUser->id],
            [
                'kode_pt' => '041104',
                'alamat'  => 'Bandung, Jawa Barat',
            ]
        );

        // ── Program Studi 1: Informatika ───────────────────
        $prodiUserA = User::query()->updateOrCreate(
            ['email' => 'informatika@gmail.com'],
            [
                'name'              => 'Program Studi Informatika',
                'password'          => Hash::make('Informatika123'),
                'role'              => UserRole::Prodi,
                'email_verified_at' => now(),
            ]
        );

        Prodi::query()->updateOrCreate(
            ['user_id' => $prodiUserA->id],
            [
                'perti_id'   => $pertiProfile->id,
                'kode_prodi' => 'IF001',
            ]
        );

        // ── Program Studi 2: Logistik ──────────────────────
        $prodiUserB = User::query()->updateOrCreate(
            ['email' => 'logistik@gmail.com'],
            [
                'name'              => 'Program Studi Logistik',
                'password'          => Hash::make('Logistik123'),
                'role'              => UserRole::Prodi,
                'email_verified_at' => now(),
            ]
        );

        Prodi::query()->updateOrCreate(
            ['user_id' => $prodiUserB->id],
            [
                'perti_id'   => $pertiProfile->id,
                'kode_prodi' => 'LG001',
            ]
        );

        $this->seedLamInfokom21();
    }

    private function seedLamInfokom21(): void
    {
        // ── Kriteria 1: Budaya Mutu ────────────────────────────────
        $m1 = Module::query()->updateOrCreate(
            ['sort_order' => 1],
            [
                'name' => 'Kriteria 1: Budaya Mutu',
                'description' => 'Sistem tata kelola, fungsi SPMI, SDM penjamin mutu, dan budaya mutu UPPS & PS.',
            ]
        );

        $this->seedRequirements($m1->id, [
            // LED PPEPP
            ['title' => '1.1 [PENETAPAN] Sistem Tata Kelola & Fungsi SPMI', 'description' => 'Dokumen kebijakan, standar, dan indikator terkait sistem tata kelola UPPS/PT serta fungsi SPMI.', 'sort_order' => 1, 'input_type' => 'document'],
            ['title' => '1.2 [PELAKSANAAN] Penerapan Tata Kelola & Kegiatan SPMI', 'description' => 'Bukti pelaksanaan standar tata kelola internal UPPS/PT (SOP, laporan tahunan) dan kegiatan SPMI.', 'sort_order' => 2, 'input_type' => 'document'],
            ['title' => '1.3 [EVALUASI] Evaluasi Standar Tata Kelola & SPMI', 'description' => 'Laporan evaluasi berkala ketercapaian standar, faktor pendukung/penghambat, dan hasil survei kepuasan.', 'sort_order' => 3, 'input_type' => 'document'],
            ['title' => '1.4 [PENGENDALIAN] Tindak Lanjut & Akar Masalah Tata Kelola', 'description' => 'Tindak lanjut (revisi & rekomendasi) terhadap akar masalah dari hasil evaluasi sistem tata kelola & SPMI.', 'sort_order' => 4, 'input_type' => 'document'],
            ['title' => '1.5 [PENINGKATAN] Optimalisasi Budaya Mutu Berkelanjutan', 'description' => 'Bukti peningkatan/optimalisasi standar tata kelola dan fungsi SPMI secara berkelanjutan.', 'sort_order' => 5, 'input_type' => 'document'],
            // Tabel LKPS
            ['title' => 'Tabel 1.A.1: Pimpinan dan Tupoksi UPPS dan PS', 'description' => 'Struktur pimpinan, periode jabatan, kualifikasi pendidikan, jabatan fungsional, dan tupoksi.', 'sort_order' => 6, 'input_type' => 'table', 'table_code' => '1.A.1'],
            ['title' => 'Tabel 1.A.2: Sumber Pendanaan UPPS/PS', 'description' => 'Realisasi penerimaan dana dari mahasiswa, yayasan, dll. dalam jutaan rupiah (TS-2, TS-1, TS).', 'sort_order' => 7, 'input_type' => 'table', 'table_code' => '1.A.2'],
            ['title' => 'Tabel 1.A.3: Penggunaan Dana UPPS/PS', 'description' => 'Alokasi penggunaan dana untuk pendidikan, penelitian, dan PkM dalam jutaan rupiah (TS-2, TS-1, TS).', 'sort_order' => 8, 'input_type' => 'table', 'table_code' => '1.A.3'],
            ['title' => 'Tabel 1.A.4: Rata-rata Beban DTPR per Semester (EWMP)', 'description' => 'Beban SKS pengajaran, penelitian, PkM, dan manajemen bagi DTPR pada TS.', 'sort_order' => 9, 'input_type' => 'table', 'table_code' => '1.A.4'],
            ['title' => 'Tabel 1.A.5: Kualifikasi Tenaga Kependidikan', 'description' => 'Jumlah tenaga kependidikan (pustakawan, laboran, teknisi, administrasi) berdasarkan jenjang pendidikan.', 'sort_order' => 10, 'input_type' => 'table', 'table_code' => '1.A.5'],
            ['title' => 'Tabel 1.B: Unit SPMI dan SDM', 'description' => 'Dokumen SPMI, jumlah auditor internal bersertifikat/non-sertifikat, frekuensi audit, dan laporan audit.', 'sort_order' => 11, 'input_type' => 'table', 'table_code' => '1.B'],
        ]);

        // ── Kriteria 2: Relevansi Pendidikan ───────────────────────
        $m2 = Module::query()->updateOrCreate(
            ['sort_order' => 2],
            [
                'name' => 'Kriteria 2: Relevansi Pendidikan',
                'description' => 'Kurikulum OBE, isi pembelajaran, capaian pembelajaran (CPL), mahasiswa, dan pelacakan lulusan.',
            ]
        );

        $this->seedRequirements($m2->id, [
            // LED PPEPP
            ['title' => '2.1 [PENETAPAN] Kurikulum OBE, Sarpras Pendidikan & Standar PMB', 'description' => 'Dokumen kebijakan kurikulum outcome-based education (KKNI level 6), keterlibatan stakeholder, dan standar PMB.', 'sort_order' => 1, 'input_type' => 'document'],
            ['title' => '2.2 [PELAKSANAAN] Pelaksanaan OBE & Fleksibilitas Pembelajaran', 'description' => 'Bukti pelaksanaan sistem OBE, fleksibilitas belajar (CBL/PBL, RPL, micro-credential), dan prestasi mahasiswa.', 'sort_order' => 2, 'input_type' => 'document'],
            ['title' => '2.3 [EVALUASI] Evaluasi Capaian Pembelajaran & Kepuasan Mahasiswa', 'description' => 'Laporan evaluasi ketercapaian CPL, ketercapaian standar kurikulum, dan hasil survei kepuasan pembelajaran.', 'sort_order' => 3, 'input_type' => 'document'],
            ['title' => '2.4 [PENGENDALIAN] Tindak Lanjut Perbaikan Pembelajaran', 'description' => 'Tindak lanjut (CAPA/revisi) terhadap akar masalah sarpras, kurikulum, dan kompetensi lulusan.', 'sort_order' => 4, 'input_type' => 'document'],
            ['title' => '2.5 [PENINGKATAN] Optimalisasi Kurikulum & Pembelajaran', 'description' => 'Bukti pembaruan kurikulum, sertifikasi kompetensi, peningkatan masa tunggu lulusan, dan serapan DUDIKA.', 'sort_order' => 5, 'input_type' => 'document'],
            // Tabel LKPS
            ['title' => 'Tabel 2.A.1: Data Mahasiswa', 'description' => 'Daya tampung, pendaftar, mahasiswa baru, dan mahasiswa aktif reguler serta RPL (TS-3 s/d TS).', 'sort_order' => 6, 'input_type' => 'table', 'table_code' => '2.A.1'],
            ['title' => 'Tabel 2.A.2: Keragaman Asal Mahasiswa', 'description' => 'Asal daerah mahasiswa (kab/kota, provinsi, negara, afirmasi, disabilitas) pada TS-2 s/d TS.', 'sort_order' => 7, 'input_type' => 'table', 'table_code' => '2.A.2'],
            ['title' => 'Tabel 2.A.3: Kondisi Jumlah Mahasiswa', 'description' => 'Rekap mahasiswa baru, mahasiswa aktif, jumlah lulusan, dan status mengundurkan diri/DO.', 'sort_order' => 8, 'input_type' => 'table', 'table_code' => '2.A.3'],
            ['title' => 'Tabel 2.B.1: Tabel Isi Pembelajaran', 'description' => 'Daftar mata kuliah, bobot SKS, semester, dan pemenuhan terhadap Profil Lulusan (PL 1 s/d PL n).', 'sort_order' => 9, 'input_type' => 'matrix', 'table_code' => '2.B.1'],
            ['title' => 'Tabel 2.B.2: Pemetaan CPL dan Profil Lulusan', 'description' => 'Matriks relasi Capaian Pembelajaran Lulusan (CPL) terhadap Profil Lulusan (PL).', 'sort_order' => 10, 'input_type' => 'matrix', 'table_code' => '2.B.2'],
            ['title' => 'Tabel 2.B.3: Peta Pemenuhan CPL', 'description' => 'Pemetaan CPL, CPMK, dan persebaran mata kuliah per semester (Semester 1 s/d 8).', 'sort_order' => 11, 'input_type' => 'matrix', 'table_code' => '2.B.3'],
            ['title' => 'Tabel 2.B.4: Rata-rata Masa Tunggu Lulusan Pertama Bekerja', 'description' => 'Data lulusan yang terlacak dan rata-rata waktu tunggu mendapatkan pekerjaan pertama (bulan).', 'sort_order' => 12, 'input_type' => 'table', 'table_code' => '2.B.4'],
            ['title' => 'Tabel 2.B.5: Kesesuaian Bidang Kerja Lulusan', 'description' => 'Profesi lulusan pada bidang Infokom vs Non-Infokom, serta lingkup tempat kerja (nasional/multinasional/wirausaha).', 'sort_order' => 13, 'input_type' => 'table', 'table_code' => '2.B.5'],
            ['title' => 'Tabel 2.B.6: Kepuasan Pengguna Lulusan', 'description' => 'Tingkat kepuasan pengguna terhadap 7 aspek kemampuan lulusan (tim, keahlian, bahasa asing, etos, dll).', 'sort_order' => 14, 'input_type' => 'table', 'table_code' => '2.B.6'],
            ['title' => 'Tabel 2.C: Fleksibilitas Dalam Proses Pembelajaran', 'description' => 'Jumlah mahasiswa dalam pembelajaran micro-credential, RPL tipe A-2, magang, dan CBL/PBL.', 'sort_order' => 15, 'input_type' => 'table', 'table_code' => '2.C'],
            ['title' => 'Tabel 2.D: Rekognisi dan Apresiasi Kompetensi Lulusan', 'description' => 'Pengakuan/apresiasi kompetensi lulusan dari masyarakat, dunia usaha, dunia industri, dan dunia kerja.', 'sort_order' => 16, 'input_type' => 'table', 'table_code' => '2.D'],
        ]);

        // ── Kriteria 3: Relevansi Penelitian ───────────────────────
        $m3 = Module::query()->updateOrCreate(
            ['sort_order' => 3],
            [
                'name' => 'Kriteria 3: Relevansi Penelitian',
                'description' => 'Sarpras riset, roadmap penelitian, pendanaan/hibah, publikasi, perolehan HKI, dan keterlibatan mahasiswa.',
            ]
        );

        $this->seedRequirements($m3->id, [
            // LED PPEPP
            ['title' => '3.1 [PENETAPAN] Kebijakan Roadmap & Standar Penelitian', 'description' => 'Dokumen roadmap riset, standar sarpras, keterlibatan mahasiswa, dan pedoman hibah/publikasi.', 'sort_order' => 1, 'input_type' => 'document'],
            ['title' => '3.2 [PELAKSANAAN] Pelaksanaan Riset & Kerjasama Penelitian', 'description' => 'Bukti konsistensi topik penelitian sesuai roadmap riset, kerjasama penelitian, dan perolehan HKI.', 'sort_order' => 2, 'input_type' => 'document'],
            ['title' => '3.3 [EVALUASI] Evaluasi Capaian Penelitian & Survei Layanan', 'description' => 'Laporan evaluasi capaian target penelitian, sumber pendanaan riset, dan survei kepuasan layanan penelitian.', 'sort_order' => 3, 'input_type' => 'document'],
            ['title' => '3.4 [PENGENDALIAN] Tindak Lanjut Akar Masalah Penelitian', 'description' => 'Tindak lanjut (revisi & rekomendasi) terhadap deviasi pelaksanaan dan pendanaan riset.', 'sort_order' => 4, 'input_type' => 'document'],
            ['title' => '3.5 [PENINGKATAN] Keberlanjutan & Optimalisasi Mutu Riset', 'description' => 'Peningkatan variasi sumber pendanaan, penguatan hilirisasi riset, dan publikasi bereputasi tinggi.', 'sort_order' => 5, 'input_type' => 'document'],
            // Tabel LKPS
            ['title' => 'Tabel 3.A.1: Sarana dan Prasarana Penelitian', 'description' => 'Laboratorium, daya tampung, luas ruang, status kepemilikan, lisensi perangkat, dan link bukti.', 'sort_order' => 6, 'input_type' => 'table', 'table_code' => '3.A.1'],
            ['title' => 'Tabel 3.A.2: Penelitian DTPR, Hibah dan Pembiayaan', 'description' => 'Judul riset, nama ketua DTPR, jumlah mahasiswa, jenis hibah, sumber (L/N/I), dan nominal pendanaan.', 'sort_order' => 7, 'input_type' => 'table', 'table_code' => '3.A.2'],
            ['title' => 'Tabel 3.A.3: Pengembangan DTPR di Bidang Penelitian', 'description' => 'Pengembangan kompetensi dosen di bidang riset (studi lanjut, pelatihan, sertifikasi riset).', 'sort_order' => 8, 'input_type' => 'table', 'table_code' => '3.A.3'],
            ['title' => 'Tabel 3.C.1: Kerjasama Penelitian', 'description' => 'Judul kerjasama riset, mitra kerjasama, tingkat (lokal/nasional/internasional), durasi, dan pendanaan.', 'sort_order' => 9, 'input_type' => 'table', 'table_code' => '3.C.1'],
            ['title' => 'Tabel 3.C.2: Publikasi Penelitian', 'description' => 'Publikasi artikel ilmiah dosen pada jurnal bereputasi, jurnal terakreditasi Sinta 1-4, dan prosiding.', 'sort_order' => 10, 'input_type' => 'table', 'table_code' => '3.C.2'],
            ['title' => 'Tabel 3.C.3: Perolehan HKI (Granted)', 'description' => 'Perolehan Hak Kekayaan Intelektual (Paten, Hak Cipta, Desain Industri) yang telah granted.', 'sort_order' => 11, 'input_type' => 'table', 'table_code' => '3.C.3'],
        ]);

        // ── Kriteria 4: Relevansi PkM ──────────────────────────────
        $m4 = Module::query()->updateOrCreate(
            ['sort_order' => 4],
            [
                'name' => 'Kriteria 4: Relevansi Pengabdian kepada Masyarakat',
                'description' => 'Sarpras PkM, roadmap pengabdian, penerapan teknologi tepat guna yang diadopsi masyarakat, dan HKI PkM.',
            ]
        );

        $this->seedRequirements($m4->id, [
            // LED PPEPP
            ['title' => '4.1 [PENETAPAN] Kebijakan Roadmap & Standar PkM', 'description' => 'Dokumen kebijakan, roadmap PkM bidang teknologi tepat guna, dan standar layanan kepakaran.', 'sort_order' => 1, 'input_type' => 'document'],
            ['title' => '4.2 [PELAKSANAAN] Pelaksanaan PkM & Diseminasi Teknologi', 'description' => 'Bukti pelaksanaan kegiatan PkM yang melibatkan mahasiswa, adopsi teknologi oleh masyarakat, dan kerjasama PkM.', 'sort_order' => 2, 'input_type' => 'document'],
            ['title' => '4.3 [EVALUASI] Evaluasi Ketercapaian PkM & Kepuasan Mitra', 'description' => 'Laporan evaluasi pelaksanaan dan keberlanjutan PkM, serta hasil survei kepuasan mitra pengabdian.', 'sort_order' => 3, 'input_type' => 'document'],
            ['title' => '4.4 [PENGENDALIAN] Tindak Lanjut Evaluasi PkM', 'description' => 'Tindak lanjut perbaikan kendala pengabdian dan penguatan kemitraan strategis dengan masyarakat/DUDIKA.', 'sort_order' => 4, 'input_type' => 'document'],
            ['title' => '4.5 [PENINGKATAN] Keberlanjutan & Optimalisasi Program PkM', 'description' => 'Peningkatan variasi sumber dana PkM, perluasan adopsi teknologi tepat guna, dan hilirisasi produk.', 'sort_order' => 5, 'input_type' => 'document'],
            // Tabel LKPS
            ['title' => 'Tabel 4.A.1: Sarana dan Prasarana PkM', 'description' => 'Laboratorium, bengkel kerja, status kepemilikan, lisensi, dan perangkat untuk kegiatan PkM.', 'sort_order' => 6, 'input_type' => 'table', 'table_code' => '4.A.1'],
            ['title' => 'Tabel 4.A.2: PkM DTPR, Hibah dan Pembiayaan PkM', 'description' => 'Judul PkM, ketua tim DTPR, mahasiswa yang terlibat, jenis hibah, sumber pendanaan (L/N/I), dan nominal.', 'sort_order' => 7, 'input_type' => 'table', 'table_code' => '4.A.2'],
            ['title' => 'Tabel 4.C.1: Kerjasama PkM', 'description' => 'Kerjasama pengabdian kepada masyarakat dengan institusi pemerintah, swasta, dan komunitas.', 'sort_order' => 8, 'input_type' => 'table', 'table_code' => '4.C.1'],
            ['title' => 'Tabel 4.C.2: Diseminasi Hasil PkM', 'description' => 'Diseminasi dan publikasi luaran PkM pada forum ilmiah atau media massa tingkat lokal, nasional, internasional.', 'sort_order' => 9, 'input_type' => 'table', 'table_code' => '4.C.2'],
            ['title' => 'Tabel 4.C.3: Perolehan HKI PkM', 'description' => 'Perolehan Hak Cipta / Paten yang dihasilkan dari kegiatan pengabdian kepada masyarakat.', 'sort_order' => 10, 'input_type' => 'table', 'table_code' => '4.C.3'],
        ]);

        // ── Kriteria 5: Akuntabilitas ──────────────────────────────
        $m5 = Module::query()->updateOrCreate(
            ['sort_order' => 5],
            [
                'name' => 'Kriteria 5: Akuntabilitas',
                'description' => 'Sistem tata kelola yang otonom, transparan, akuntabel, dan didukung sistem informasi terintegrasi.',
            ]
        );

        $this->seedRequirements($m5->id, [
            // LED PPEPP
            ['title' => '5.1 [PENETAPAN] Standar Tata Kelola Otonom & Transparan', 'description' => 'Kebijakan sistem tata kelola otonom, akuntabel, transparan, dan pedoman audit mutu tupoksi.', 'sort_order' => 1, 'input_type' => 'document'],
            ['title' => '5.2 [PELAKSANAAN] Penerapan Tata Kelola & Sistem Informasi', 'description' => 'Bukti berfungsinya sistem informasi (akademik, keuangan, SDM, sarpras) dan SDM pengelola.', 'sort_order' => 2, 'input_type' => 'document'],
            ['title' => '5.3 [EVALUASI] Evaluasi Efektivitas Tata Kelola & Audit Mutu', 'description' => 'Laporan audit mutu internal atas tupoksi organisasi, sarpras pendidikan, dan evaluasi keterbukaan informasi.', 'sort_order' => 3, 'input_type' => 'document'],
            ['title' => '5.4 [PENGENDALIAN] Tindak Lanjut Temuan Tata Kelola', 'description' => 'Tindak lanjut terhadap hasil audit mutu dan kepatuhan prosedur operasional.', 'sort_order' => 4, 'input_type' => 'document'],
            ['title' => '5.5 [PENINGKATAN] Optimalisasi Tata Kelola Terintegrasi', 'description' => 'Pengembangan berkelanjutan sistem informasi terintegrasi dan kapasitas SDM pengelola.', 'sort_order' => 5, 'input_type' => 'document'],
            // Tabel LKPS
            ['title' => 'Tabel 5.1: Sistem Tata Kelola', 'description' => 'Daftar sistem informasi (Pendidikan, Keuangan, SDM, Sarpras, SPMI), jenis akses, unit pengelola, link bukti.', 'sort_order' => 6, 'input_type' => 'table', 'table_code' => '5.1'],
            ['title' => 'Tabel 5.2: Sarana dan Prasarana Pendidikan', 'description' => 'Prasarana pembelajaran (ruang kelas, laboratorium, perpustakaan), daya tampung, luas, kepemilikan, lisensi.', 'sort_order' => 7, 'input_type' => 'table', 'table_code' => '5.2'],
        ]);

        // ── Kriteria 6: Diferensiasi Misi ──────────────────────────
        $m6 = Module::query()->updateOrCreate(
            ['sort_order' => 6],
            [
                'name' => 'Kriteria 6: Diferensiasi Misi',
                'description' => 'Kesesuaian VMTS (Visi, Misi, Tujuan, Strategi) dengan ciri khas keilmuan program studi dan kebutuhan DUDIKA.',
            ]
        );

        $this->seedRequirements($m6->id, [
            // LED PPEPP
            ['title' => '6.1 [PENETAPAN] VMTS & Ciri Khas Keilmuan Program Studi', 'description' => 'Dokumen penetapan visi keilmuan prodi yang selaras dengan VMTS PT/UPPS dan rencana strategis.', 'sort_order' => 1, 'input_type' => 'document'],
            ['title' => '6.2 [PELAKSANAAN] Implementasi Diferensiasi Misi Tridarma', 'description' => 'Bukti pelaksanaan kegiatan tridarma yang mencerminkan ciri khas keilmuan dan apresiasi DUDIKA.', 'sort_order' => 2, 'input_type' => 'document'],
            ['title' => '6.3 [EVALUASI] Evaluasi Pemahaman & Ketercapaian VMTS', 'description' => 'Laporan survei pemahaman VMTS oleh sivitas akademika dan evaluasi pencapaian diferensiasi misi.', 'sort_order' => 3, 'input_type' => 'document'],
            ['title' => '6.4 [PENGENDALIAN] Tindak Lanjut Kesesuaian Visi Keilmuan', 'description' => 'Tindak lanjut atas hasil evaluasi kesesuaian implementasi terhadap visi keilmuan prodi.', 'sort_order' => 4, 'input_type' => 'document'],
            ['title' => '6.5 [PENINGKATAN] Penguatan Keunggulan Kompetitif Keilmuan', 'description' => 'Peningkatan daya saing prodi berdasarkan keunikan diferensiasi misi di tingkat nasional dan global.', 'sort_order' => 5, 'input_type' => 'document'],
            // Tabel LKPS
            ['title' => 'Tabel 6: Kesesuaian Visi, Misi', 'description' => 'Tabel keselarasan antara Visi PT, Visi UPPS, Visi Keilmuan PS, Misi PT, dan Misi UPPS.', 'sort_order' => 6, 'input_type' => 'table', 'table_code' => '6'],
        ]);

        // ── Suplemen Program Studi (Sarjana Terapan) ───────────────
        $m7 = Module::query()->updateOrCreate(
            ['sort_order' => 7],
            [
                'name' => 'Suplemen Program Studi: Sarjana Terapan',
                'description' => 'Pemenuhan kurikulum inti sesuai bidang ilmu komputer, domain spesifik, matematika, dan Capstone Project.',
            ]
        );

        $this->seedRequirements($m7->id, [
            ['title' => 'D.1 Mata Kuliah Inti / Khas Bidang Program Studi', 'description' => 'Daftar dan RPS mata kuliah fundamental dan praktik terapan sesuai rumpun ilmu prodi (IF/SI/TI/RPL/Data Science/SK).', 'sort_order' => 1, 'input_type' => 'document'],
            ['title' => 'D.2 Mata Kuliah Domain Spesifik & Lingkungan Keilmuan', 'description' => 'Pemetaan kurikulum merujuk standar internasional (ACM/IEEE-CS) dan RPS domain spesifik.', 'sort_order' => 2, 'input_type' => 'document'],
            ['title' => 'D.3 Mata Kuliah Matematika & Ilmu Dasar Terkait', 'description' => 'Struktur kurikulum dan RPS mata kuliah matematika, statistika, kalkulus, atau aljabar linier.', 'sort_order' => 3, 'input_type' => 'document'],
            ['title' => 'D.4 Proyek Utama (Capstone Project)', 'description' => 'Panduan pelaksanaan, daftar integrasi mata kuliah, rancangan kurikulum, dan laporan proyek utama riil.', 'sort_order' => 4, 'input_type' => 'document'],
        ]);

        // Hapus modul lama nomor 8 dan 9 jika ada (karena sistem lama pakai 9 modul)
        Module::query()->where('sort_order', '>', 7)->delete();
    }

    private function seedRequirements(int $moduleId, array $rows): void
    {
        foreach ($rows as $row) {
            Requirement::query()->updateOrCreate(
                [
                    'module_id' => $moduleId,
                    'title' => $row['title'],
                ],
                [
                    'description' => $row['description'] ?? null,
                    'input_type'  => $row['input_type'] ?? 'document',
                    'table_code'  => $row['table_code'] ?? null,
                    'sort_order'  => $row['sort_order'],
                ]
            );
        }
    }
}
