<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessedWebhookEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Check if an event ID has already been processed for this tenant.
     *
     * @param int $organizationId
     * @param string $eventId
     * @return bool
     */
    public static function hasBeenProcessed(int $organizationId, string $eventId): bool
    {
        return static::where('organization_id', $organizationId)
            ->where('event_id', $eventId)
            ->exists();
    }
}
