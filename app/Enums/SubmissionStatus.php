<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Pending = 'pending';
    case Uploaded = 'uploaded';
    case Approved = 'approved';
    case Revision = 'revision';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu unggah',
            self::Uploaded => 'Menunggu validasi',
            self::Approved => 'Sesuai',
            self::Revision => 'Perlu revisi',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-slate-100 text-slate-700 ring-slate-500/15',
            self::Uploaded => 'bg-amber-50 text-amber-800 ring-amber-500/20',
            self::Approved => 'bg-emerald-50 text-emerald-800 ring-emerald-500/20',
            self::Revision => 'bg-rose-50 text-rose-800 ring-rose-500/20',
        };
    }
}

