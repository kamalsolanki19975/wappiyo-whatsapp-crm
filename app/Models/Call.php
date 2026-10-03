<?php

namespace App\Models;

use App\Helpers\DateTimeHelper;
use App\Http\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Call extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'connected_at' => 'datetime',
        'ended_at' => 'datetime',
        'follow_up_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_duration',
        'formatted_phone_number',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function chatLogs()
    {
        return $this->hasMany(ChatLog::class, 'entity_id')->where('entity_type', 'call');
    }

    // Accessors for organization timezone formatting
    public function getStartedAtAttribute($value)
    {
        return $value ? DateTimeHelper::convertToOrganizationTimezone($value)?->toDateTimeString() : null;
    }

    public function getConnectedAtAttribute($value)
    {
        return $value ? DateTimeHelper::convertToOrganizationTimezone($value)?->toDateTimeString() : null;
    }

    public function getEndedAtAttribute($value)
    {
        return $value ? DateTimeHelper::convertToOrganizationTimezone($value)?->toDateTimeString() : null;
    }

    public function getFollowUpAtAttribute($value)
    {
        return $value ? DateTimeHelper::convertToOrganizationTimezone($value)?->toDateTimeString() : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? DateTimeHelper::convertToOrganizationTimezone($value)?->toDateTimeString() : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? DateTimeHelper::convertToOrganizationTimezone($value)?->toDateTimeString() : null;
    }

    public function getFormattedDurationAttribute()
    {
        $duration = (int) ($this->attributes['duration'] ?? 0);
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $seconds = $duration % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getFormattedPhoneNumberAttribute()
    {
        $phone = $this->customer_phone ?? '';
        if (!$phone) {
            return '';
        }

        try {
            return phone($phone)->formatInternational();
        } catch (\Throwable $e) {
            return $phone;
        }
    }
}
