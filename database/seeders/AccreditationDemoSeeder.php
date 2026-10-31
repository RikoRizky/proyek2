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
 * Data demo: admin, perguruan tinggi, program studi, modul, dan persyaratan.
 * Akun program studi tambahan sebaiknya dibuat lewat panel Perti → Kelola Program Studi.
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

        // Buat/update profil perti di tabel pertis
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

        $this->seedKriteria9();
    }

    private function seedKriteria9(): void
    {
        // Model: 1 kriteria = 1 Module, lalu butir yang diunggah prodi = Requirement.
        $m1 = Module::query()->updateOrCreate(
            ['sort_order' => 1],
            [
                'name' => 'Kriteria 1: Visi, Misi, Tujuan, dan Strategi',
                'description' => 'Arah, komitmen, dan target program studi.',
            ]
        );

        $m2 = Module::query()->updateOrCreate(
            ['sort_order' => 2],
            [
                'name' => 'Kriteria 2: Tata Pamong, Tata Kelola, dan Kerjasama',
                'description' => 'Sistem kepemimpinan, penjaminan mutu, dan kolaborasi.',
            ]
        );

        $m3 = Module::query()->updateOrCreate(
            ['sort_order' => 3],
            [
                'name' => 'Kriteria 3: Mahasiswa',
                'description' => 'Kualitas input, rasio mahasiswa, layanan, dan prestasi.',
            ]
        );

        $m4 = Module::query()->updateOrCreate(
            ['sort_order' => 4],
            [
                'name' => 'Kriteria 4: Sumber Daya Manusia (SDM)',
                'description' => 'Kualifikasi, kompetensi, dan beban kerja dosen & tenaga kependidikan.',
            ]
        );

        $m5 = Module::query()->updateOrCreate(
            ['sort_order' => 5],
            [
                'name' => 'Kriteria 5: Keuangan, Sarana, dan Prasarana',
                'description' => 'Kecukupan dana, fasilitas pembelajaran, dan laboratorium.',
            ]
        );

        $m6 = Module::query()->updateOrCreate(
            ['sort_order' => 6],
            [
                'name' => 'Kriteria 6: Pendidikan',
                'description' => 'Kurikulum, proses pembelajaran, dan capaian pembelajaran.',
            ]
        );

        $m7 = Module::query()->updateOrCreate(
            ['sort_order' => 7],
            [
                'name' => 'Kriteria 7: Penelitian',
                'description' => 'Kuantitas, kualitas, dan relevansi penelitian dosen & mahasiswa.',
            ]
        );

        $m8 = Module::query()->updateOrCreate(
            ['sort_order' => 8],
            [
                'name' => 'Kriteria 8: Pengabdian kepada Masyarakat',
                'description' => 'Penerapan ilmu dan teknologi yang bermanfaat langsung bagi publik.',
            ]
        );

        $m9 = Module::query()->updateOrCreate(
            ['sort_order' => 9],
            [
                'name' => 'Kriteria 9: Luaran dan Capaian',
                'description' => 'Capaian Tridharma, prestasi, dan keberhasilan lulusan.',
            ]
        );

        // Requirement berdasarkan siklus mutu: Penetapan, Pelaksanaan, Evaluasi, Pengendalian, Peningkatan.
        $this->seedRequirements($m1->id, [
            ['title' => 'Penetapan: Standar visi, misi, tujuan, dan strategi prodi', 'description' => 'Dokumen penetapan standar/arah prodi (visi, misi, tujuan, strategi) yang disahkan.', 'sort_order' => 1],
            ['title' => 'Pelaksanaan: Implementasi visi, misi, tujuan, dan strategi', 'description' => 'Bukti penerapan program/proses kerja harian yang selaras dengan visi, misi, tujuan, dan strategi.', 'sort_order' => 2],
            ['title' => 'Evaluasi: Penilaian ketercapaian visi, misi, tujuan, dan strategi', 'description' => 'Laporan evaluasi/evidence capaian dan analisis terhadap target yang direncanakan.', 'sort_order' => 3],
            ['title' => 'Pengendalian: Tindakan korektif atas penyimpangan', 'description' => 'Rekap temuan, analisis akar masalah, dan tindakan korektif/preventif bila terjadi deviasi.', 'sort_order' => 4],
            ['title' => 'Peningkatan: Perbaikan berkelanjutan standar', 'description' => 'Dokumen pembaruan standar dan peningkatan kinerja berdasarkan hasil evaluasi.', 'sort_order' => 5],
        ]);

        $this->seedRequirements($m2->id, [
            ['title' => 'Penetapan: Standar tata pamong, tata kelola, dan kerjasama', 'description' => 'Dokumen penetapan struktur, mekanisme kepemimpinan, tata kelola, dan kebijakan kerjasama.', 'sort_order' => 1],
            ['title' => 'Pelaksanaan: Penerapan tata pamong, tata kelola, dan kerjasama', 'description' => 'Bukti pelaksanaan rapat/keputusan, koordinasi, serta implementasi program kolaborasi/kerjasama.', 'sort_order' => 2],
            ['title' => 'Evaluasi: Evaluasi efektivitas tata pamong, tata kelola, dan kerjasama', 'description' => 'Laporan evaluasi, audit internal/eksternal (jika ada), dan perbandingan capaian vs standar.', 'sort_order' => 3],
            ['title' => 'Pengendalian: Tindakan korektif dan perbaikan sistem', 'description' => 'Notulen tindak lanjut, CAPA (corrective action), dan penyesuaian prosedur bila ada penyimpangan.', 'sort_order' => 4],
            ['title' => 'Peningkatan: Inovasi/peningkatan mutu tata kelola', 'description' => 'Bukti perbaikan berkelanjutan pada sistem tata pamong/tata kelola dan kualitas kolaborasi.', 'sort_order' => 5],
        ]);

        $this->seedRequirements($m3->id, [
            ['title' => 'Penetapan: Standar kualitas input, layanan, dan prestasi mahasiswa', 'description' => 'Dokumen penetapan standar rekrutmen, layanan akademik/nonakademik, serta pengembangan prestasi.', 'sort_order' => 1],
            ['title' => 'Pelaksanaan: Penerapan standar terhadap mahasiswa', 'description' => 'Bukti pelaksanaan layanan, pembinaan, dan program peningkatan kualitas mahasiswa sesuai standar.', 'sort_order' => 2],
            ['title' => 'Evaluasi: Evaluasi kualitas input, layanan, dan prestasi', 'description' => 'Laporan evaluasi (mis. tracer awal/monitoring prestasi), perbandingan capaian vs target.', 'sort_order' => 3],
            ['title' => 'Pengendalian: Koreksi atas temuan layanan/kualitas', 'description' => 'Tindak lanjut temuan (mis. kendala layanan, deviasi rasio/kapasitas) dan perbaikan proses.', 'sort_order' => 4],
            ['title' => 'Peningkatan: Perbaikan berkelanjutan layanan dan prestasi', 'description' => 'Bukti peningkatan (program baru, revisi kebijakan, hasil perbaikan) berdasarkan evaluasi.', 'sort_order' => 5],
        ]);

        $this->seedRequirements($m4->id, [
            ['title' => 'Penetapan: Standar SDM (dosen & tenaga kependidikan)', 'description' => 'Dokumen penetapan standar kualifikasi, kompetensi, beban kerja, dan pengembangan SDM.', 'sort_order' => 1],
            ['title' => 'Pelaksanaan: Penerapan standar SDM', 'description' => 'Bukti pelaksanaan (penugasan, peningkatan kompetensi, pemenuhan beban kerja sesuai standar).', 'sort_order' => 2],
            ['title' => 'Evaluasi: Evaluasi capaian kompetensi dan beban kerja', 'description' => 'Laporan evaluasi terhadap capaian kompetensi, hasil kinerja, dan kesesuaian beban kerja.', 'sort_order' => 3],
            ['title' => 'Pengendalian: Tindakan korektif penguatan SDM', 'description' => 'Rekap temuan/ketidaksesuaian dan rencana korektif (mis. gap kompetensi, ketidakteraturan beban).', 'sort_order' => 4],
            ['title' => 'Peningkatan: Pengembangan berkelanjutan SDM', 'description' => 'Bukti program peningkatan berkelanjutan (pelatihan, sertifikasi, coaching) dan dampaknya.', 'sort_order' => 5],
        ]);

        $this->seedRequirements($m5->id, [
            ['title' => 'Penetapan: Standar keuangan serta sarana dan prasarana', 'description' => 'Dokumen penetapan standar anggaran, prioritas pengadaan, dan standar pemanfaatan sarpras.', 'sort_order' => 1],
            ['title' => 'Pelaksanaan: Penerapan standar keuangan dan sarpras', 'description' => 'Bukti realisasi anggaran dan pemanfaatan sarpras (lab, ruang, perangkat) untuk kegiatan prodi.', 'sort_order' => 2],
            ['title' => 'Evaluasi: Evaluasi kecukupan dan efektivitas sarpras', 'description' => 'Laporan evaluasi pemenuhan standar, rekonsiliasi anggaran/realisasi, dan dampak pemanfaatan.', 'sort_order' => 3],
            ['title' => 'Pengendalian: Tindakan korektif sarpras & keuangan', 'description' => 'Tindak lanjut temuan (kekurangan, keterlambatan, ketidaksesuaian pemakaian) dan koreksi anggaran/proses.', 'sort_order' => 4],
            ['title' => 'Peningkatan: Perbaikan berkelanjutan sarpras & pengelolaan keuangan', 'description' => 'Bukti peningkatan layanan/fasilitas dan penguatan tata kelola keuangan berdasarkan evaluasi.', 'sort_order' => 5],
        ]);

        $this->seedRequirements($m6->id, [
            ['title' => 'Penetapan: Standar kurikulum dan proses pembelajaran', 'description' => 'Dokumen penetapan kurikulum, CPL, struktur kurikulum, serta standar mutu proses pembelajaran.', 'sort_order' => 1],
            ['title' => 'Pelaksanaan: Penerapan kurikulum dan proses pembelajaran', 'description' => 'Bukti implementasi (RPS, kontrak perkuliahan, metode pembelajaran, evaluasi pembelajaran).', 'sort_order' => 2],
            ['title' => 'Evaluasi: Evaluasi capaian pembelajaran', 'description' => 'Laporan evaluasi capaian CPL dan analisis hasil pembelajaran dibanding standar/target.', 'sort_order' => 3],
            ['title' => 'Pengendalian: Tindakan korektif perbaikan pembelajaran', 'description' => 'Rekap temuan pembelajaran dan tindakan korektif (perbaikan metode, RPS, penilaian).', 'sort_order' => 4],
            ['title' => 'Peningkatan: Peningkatan berkelanjutan kurikulum & pembelajaran', 'description' => 'Bukti pembaruan kurikulum/proses dan peningkatan kualitas pembelajaran dari hasil evaluasi.', 'sort_order' => 5],
        ]);

        $this->seedRequirements($m7->id, [
            ['title' => 'Penetapan: Standar penelitian (kuantitas & kualitas)', 'description' => 'Dokumen penetapan standar roadmap penelitian, kriteria/skim, indikator kuantitas dan kualitas penelitian.', 'sort_order' => 1],
            ['title' => 'Pelaksanaan: Pelaksanaan penelitian', 'description' => 'Bukti pelaksanaan kegiatan penelitian sesuai standar (skema, kegiatan, luaran sementara).', 'sort_order' => 2],
            ['title' => 'Evaluasi: Evaluasi hasil penelitian', 'description' => 'Laporan evaluasi hasil penelitian (publikasi/HKI/kinerja) dibandingkan standar.', 'sort_order' => 3],
            ['title' => 'Pengendalian: Koreksi terhadap deviasi penelitian', 'description' => 'Tindak lanjut atas temuan (mis. keterlambatan, capaian belum memenuhi target) dan perbaikan.', 'sort_order' => 4],
            ['title' => 'Peningkatan: Perbaikan berkelanjutan mutu penelitian', 'description' => 'Bukti peningkatan kualitas penelitian (strategi, kolaborasi, perbaikan proses) berdasarkan evaluasi.', 'sort_order' => 5],
        ]);

        $this->seedRequirements($m8->id, [
            ['title' => 'Penetapan: Standar pengabdian kepada masyarakat', 'description' => 'Dokumen penetapan standar roadmap/peta kebutuhan pengabdian, skema, dan indikator luaran.', 'sort_order' => 1],
            ['title' => 'Pelaksanaan: Penerapan standar pengabdian', 'description' => 'Bukti pelaksanaan program pengabdian sesuai rencana (kegiatan, mitra, proses).', 'sort_order' => 2],
            ['title' => 'Evaluasi: Evaluasi dampak pengabdian', 'description' => 'Laporan evaluasi luaran dan dampak pengabdian, dibanding standar dan target.', 'sort_order' => 3],
            ['title' => 'Pengendalian: Tindakan korektif pengabdian', 'description' => 'Rekap temuan dan tindakan korektif (perbaikan metode, penguatan mitra, penyesuaian program).', 'sort_order' => 4],
            ['title' => 'Peningkatan: Peningkatan berkelanjutan pengabdian', 'description' => 'Bukti perbaikan/peningkatan program pengabdian dan kualitas luaran berdasarkan evaluasi.', 'sort_order' => 5],
        ]);

        $this->seedRequirements($m9->id, [
            ['title' => 'Penetapan: Standar luaran tridharma dan keberhasilan lulusan', 'description' => 'Dokumen penetapan standar capaian luaran (penelitian/pengabdian) dan indikator keberhasilan lulusan.', 'sort_order' => 1],
            ['title' => 'Pelaksanaan: Penerapan standar luaran dan tracer study', 'description' => 'Bukti pelaksanaan pengukuran luaran dan pelaksanaan tracer study/monitoring lulusan.', 'sort_order' => 2],
            ['title' => 'Evaluasi: Evaluasi capaian luaran dan keberhasilan lulusan', 'description' => 'Laporan evaluasi capaian luaran dan hasil tracer study dibanding standar/target.', 'sort_order' => 3],
            ['title' => 'Pengendalian: Koreksi jika capaian tidak memenuhi standar', 'description' => 'Tindak lanjut temuan (mis. capaian luaran, serapan lulusan) dan corrective action.', 'sort_order' => 4],
            ['title' => 'Peningkatan: Perbaikan berkelanjutan capaian', 'description' => 'Bukti peningkatan berkelanjutan berbasis evaluasi (program perbaikan, strategi peningkatan).', 'sort_order' => 5],
        ]);
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
                    'sort_order' => $row['sort_order'],
                ]
            );
        }
    }
}
