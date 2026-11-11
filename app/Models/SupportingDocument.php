<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportingDocument extends Model
{
    use HasFactory;

    /**
     * Kategori standar selaras dengan Instrumen LAM INFOKOM 2.1 (LED, LKPS, dan Lampiran).
     * Pengguna tetap dapat mengetik kategori kustom sesuai kebutuhan.
     *
     * @var list<string>
     */
    public const DEFAULT_CATEGORIES = [
        'Dokumen Mahasiswa',
        'Data Nilai & CPL',
        'Kurikulum, RPS & Capstone',
        'SK & Legalitas Identitas Pengusul',
        'Dosen & Tenaga Kependidikan',
        'Penelitian, PkM & Luaran',
        'Alumni, Tracer Study & DUDIKA',
        'Dokumen Lainnya',
    ];

    /**
     * Pilihan tahun akademik / TS standar LKPS & LED.
     *
     * @var list<string>
     */
    public const ACADEMIC_YEARS = [
        'TS',
        'TS-1',
        'TS-2',
        'TS-3',
        'Umum',
    ];

    /**
     * Template cepat untuk memudahkan dosen/admin prodi/perti menambah dokumen pendukung umum.
     *
     * @var list<array<string, string|null>>
     */
    public const PRESETS = [
        [
            'label' => 'Kumpulan Data Mahasiswa',
            'category' => 'Dokumen Mahasiswa',
            'title' => 'Kumpulan Data Mahasiswa Aktif & Status Registrasi',
            'academic_year' => 'TS',
            'table_code' => '2.A.1',
            'description' => 'Rekapitulasi data mahasiswa aktif, status registrasi, asal daerah, dan sebaran semester sesuai PDDikti (dukung Tabel LKPS 2.A.1).',
        ],
        [
            'label' => 'Data Nilai & CPL',
            'category' => 'Data Nilai & CPL',
            'title' => 'Rekap Data Nilai Mahasiswa & Pengukuran CPL',
            'academic_year' => 'TS',
            'table_code' => '2.B.3',
            'description' => 'Kumpulan nilai mata kuliah, sampel transkrip, serta bukti pengukuran ketercapaian CPL (dukung Tabel LKPS 2.B.2 & 2.B.3).',
        ],
        [
            'label' => 'SK PT, Pembukaan & Akreditasi PS',
            'category' => 'SK & Legalitas Identitas Pengusul',
            'title' => 'SK Pendirian PT, Pembukaan PS & SK Akreditasi Terakhir',
            'academic_year' => 'Umum',
            'table_code' => null,
            'description' => 'Lampiran 1 LED Identitas Pengusul: SK Pendirian Perguruan Tinggi, SK Izin Pembukaan Program Studi, dan SK Akreditasi.',
        ],
        [
            'label' => 'Struktur Organisasi (Gambar 1.A)',
            'category' => 'SK & Legalitas Identitas Pengusul',
            'title' => 'Bagan Struktur Organisasi UPPS & Program Studi (Gambar 1.A)',
            'academic_year' => 'Umum',
            'table_code' => '1.A.1',
            'description' => 'Struktur organisasi UPPS/PS, unit penjaminan mutu internal, serta tupoksi pimpinan (Gambar 1.A & Tabel 1.A.1).',
        ],
        [
            'label' => 'RPS & Capstone Project',
            'category' => 'Kurikulum, RPS & Capstone',
            'title' => 'Kumpulan RPS Mata Kuliah & Portofolio Capstone Project',
            'academic_year' => 'TS',
            'table_code' => '2.B.5',
            'description' => 'Dokumen RPS bidang Komputasi/Informatika beserta portofolio, rubrik, dan hasil Capstone Project mahasiswa (Suplemen LED).',
        ],
        [
            'label' => 'Tracer Study & Kepuasan Pengguna',
            'category' => 'Alumni, Tracer Study & DUDIKA',
            'title' => 'Laporan Tracer Study & Survei Kepuasan Pengguna Lulusan',
            'academic_year' => 'TS',
            'table_code' => '2.C',
            'description' => 'Bukti pelacakan alumni, masa tunggu kerja, kesesuaian bidang, serta kuesioner kepuasan pengguna (Tabel 2.C & 2.D).',
        ],
    ];

    protected $fillable = [
        'perti_id',
        'prodi_id',
        'uploaded_by',
        'category',
        'title',
        'academic_year',
        'description',
        'requirement_id',
        'table_code',
        'files',
        'google_drive_links',
    ];

    protected function casts(): array
    {
        return [
            'files' => 'array',
            'google_drive_links' => 'array',
        ];
    }

    public function perti(): BelongsTo
    {
        return $this->belongsTo(Perti::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(Requirement::class);
    }

    public function isPtShared(): bool
    {
        return $this->prodi_id === null;
    }

    public function scopeLabel(): string
    {
        if ($this->prodi) {
            return $this->prodi->name;
        }

        return 'Perguruan Tinggi / UPPS (Semua Prodi)';
    }

    public function canBeEditedBy(User $user): bool
    {
        if ($user->isPerti()) {
            $pertiId = $user->pertiProfile?->id;

            return $pertiId !== null && (int) $this->perti_id === (int) $pertiId;
        }

        if ($user->isProdi()) {
            $prodiId = $user->prodiProfile?->id;

            if ($prodiId !== null && (int) $this->prodi_id === (int) $prodiId) {
                return true;
            }

            return $this->prodi_id === null && (int) $this->uploaded_by === (int) $user->id;
        }

        return false;
    }

    public function canBeViewedBy(User $user): bool
    {
        if ($this->canBeEditedBy($user)) {
            return true;
        }

        if ($user->isProdi()) {
            $pertiId = $user->prodiProfile?->perti_id;

            return $pertiId !== null
                && (int) $this->perti_id === (int) $pertiId
                && $this->prodi_id === null;
        }

        return false;
    }
}
