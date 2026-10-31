<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LkpsSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requirement_id',
        'table_code',
        'table_name',
        'dynamic_headers',
        'table_rows',
        'summary_data',
        'status',
        'validation_notes',
        'validated_at',
        'validated_by',
    ];

    protected function casts(): array
    {
        return [
            'dynamic_headers' => 'array',
            'table_rows' => 'array',
            'summary_data' => 'array',
            'validated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(Requirement::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'approved' => 'bg-emerald-50 text-emerald-800 ring-emerald-500/20',
            'revision' => 'bg-rose-50 text-rose-800 ring-rose-500/20',
            'submitted', 'uploaded' => 'bg-amber-50 text-amber-800 ring-amber-500/20',
            default => 'bg-slate-100 text-slate-700 ring-slate-500/15',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'approved' => 'Sesuai',
            'revision' => 'Perlu revisi',
            'submitted', 'uploaded' => 'Menunggu validasi',
            'draft' => 'Draft tersimpan',
            default => 'Belum diisi',
        };
    }
}

