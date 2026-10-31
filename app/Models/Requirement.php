<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'description',
        'input_type',
        'table_code',
        'sort_order',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function lkpsSubmissions(): HasMany
    {
        return $this->hasMany(LkpsSubmission::class);
    }

    public function isTable(): bool
    {
        return in_array($this->input_type, ['table', 'matrix', 'hybrid']);
    }

    public function isDocument(): bool
    {
        return in_array($this->input_type, ['document', 'hybrid']);
    }
}
