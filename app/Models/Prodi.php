<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prodi extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $table = 'prodis';

    protected $fillable = [
        'user_id',
        'perti_id',
        'kode_prodi',
    ];

    /**
     * Akun login (User) dari Prodi ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Perguruan Tinggi induk dari Prodi ini.
     */
    public function perti(): BelongsTo
    {
        return $this->belongsTo(Perti::class, 'perti_id');
    }

    /**
     * Nama prodi diambil dari akun user-nya.
     */
    public function getNameAttribute(): string
    {
        return $this->user?->name ?? '';
    }

    /**
     * Email diambil dari akun user-nya.
     */
    public function getEmailAttribute(): string
    {
        return $this->user?->email ?? '';
    }
}
