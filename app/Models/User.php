<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'role',
        'password',
        'profile_photo_path',
        'active_package',
        'package_valid_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'            => 'hashed',
            'role'                => UserRole::class,
            'package_valid_until' => 'datetime',
        ];
    }

    // ─── Role helpers ─────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isPerti(): bool
    {
        return $this->role === UserRole::Perti;
    }

    public function isProdi(): bool
    {
        return $this->role === UserRole::Prodi;
    }

    // ─── Relations ────────────────────────────────────────────────────

    /**
     * Profil Perguruan Tinggi untuk akun dengan role=perti.
     */
    public function pertiProfile(): HasOne
    {
        return $this->hasOne(Perti::class, 'user_id');
    }

    /**
     * Profil Program Studi untuk akun dengan role=prodi.
     */
    public function prodiProfile(): HasOne
    {
        return $this->hasOne(Prodi::class, 'user_id');
    }

    /**
     * Semua submission yang diunggah oleh user ini.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    // ─── Accessories ─────────────────────────────────────────────────

    public function getEffectivePackageAttribute()
    {
        if ($this->isProdi() && $this->prodiProfile && $this->prodiProfile->perti && $this->prodiProfile->perti->user) {
            return $this->prodiProfile->perti->user->active_package;
        }
        return $this->active_package;
    }

    public function getEffectivePackageValidUntilAttribute()
    {
        if ($this->isProdi() && $this->prodiProfile && $this->prodiProfile->perti && $this->prodiProfile->perti->user) {
            return $this->prodiProfile->perti->user->package_valid_until;
        }
        return $this->package_valid_until;
    }

    public function isSubscriptionExpired(): bool
    {
        if ($this->isAdmin()) {
            return false;
        }
        $validUntil = $this->effective_package_valid_until;
        return $validUntil && $validUntil < now();
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path
            ? asset('uploads/profile_photos/' . $this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}
