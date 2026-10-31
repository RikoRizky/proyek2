<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'requirement_id',
        'user_id',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'status',
        'version',
        'is_latest',
        'google_drive_links',
        'files',
        'validation_notes',
        'validated_at',
        'validated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubmissionStatus::class,
            'is_latest' => 'boolean',
            'google_drive_links' => 'array',
            'files' => 'array',
            'validated_at' => 'datetime',
        ];
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(Requirement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function scopeLatestForUnit(Builder $query): Builder
    {
        return $query->where('is_latest', true);
    }
}
