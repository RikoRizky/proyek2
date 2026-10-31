<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sort_order',
    ];

    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class)->orderBy('sort_order');
    }

    public function shortLabel(): string
    {
        if (preg_match('/^(Kriteria \d+)/', $this->name, $matches)) {
            return $matches[1];
        }

        return \Illuminate\Support\Str::limit($this->name, 32);
    }
}
