<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'otp',
        'expires_at',
        'attempts',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    /**
     * Determine if OTP has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->isAfter($this->expires_at);
    }

    /**
     * Verify provided OTP against stored hash/value.
     */
    public function isValid(string $inputOtp): bool
    {
        if ($this->isExpired() || $this->attempts >= 5 || $this->verified_at !== null) {
            return false;
        }

        // Support hashed OTP with Hash::check, or exact match
        if (\Illuminate\Support\Facades\Hash::check($inputOtp, $this->otp) || $this->otp === $inputOtp) {
            return true;
        }

        return false;
    }
}
