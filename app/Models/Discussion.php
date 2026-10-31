<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'whatsapp',
        'perusahaan',
        'jabatan',
        'kebutuhan',
        'kebutuhan_lainnya',
        'sistem_saat_ini',
        'investasi',
    ];

    protected $casts = [
        'kebutuhan' => 'array',
    ];

    /**
     * Label display untuk sistem_saat_ini
     */
    public function sistemLabel(): string
    {
        return match ($this->sistem_saat_ini) {
            'internal'  => 'Sistem Internal',
            'vendor'    => 'Mitra/Vendor',
            'community' => 'Sistem Komunitas',
            'none'      => 'Belum Ada Sistem',
            default     => $this->sistem_saat_ini,
        };
    }

    /**
     * Label display untuk investasi
     */
    public function investasiLabel(): string
    {
        return match ($this->investasi) {
            'near'     => 'Siap dalam waktu dekat',
            'budgeted' => 'Sudah dianggarkan',
            'planning' => 'Sedang perencanaan',
            'next'     => 'Periode selanjutnya',
            default    => $this->investasi,
        };
    }
}
