<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Perti extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $table = 'pertis';

    protected $fillable = [
        'user_id',
        'kode_pt',
        'alamat',
    ];

    /**
     * Akun login (User) dari Perti ini.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Daftar Prodi yang berada di bawah Perti ini.
     */
    public function prodis(): HasMany
    {
        return $this->hasMany(Prodi::class, 'perti_id');
    }

    /**
     * Nama perti diambil dari akun user-nya.
     */
    public function getNameAttribute(): string
    {
        return $this->user?->name ?? '';
    }
}
