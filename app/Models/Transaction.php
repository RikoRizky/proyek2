<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'package_name',
        'amount',
        'customer_name',
        'customer_email',
        'status',
        'snap_token',
        'registration_token',
        'is_registered',
        'user_id',
        'duration_years',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
