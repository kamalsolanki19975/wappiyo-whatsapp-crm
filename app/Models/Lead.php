<?php

namespace App\Models;

use App\Helpers\DateTimeHelper;
use App\Http\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Lead extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'company',
        'subject',
        'message',
        'form_type',
        'source',
        'page_url',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'ip_address',
        'user_agent',
        'status',
        'assigned_to',
        'converted_to_organization_id',
        'converted_at',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'notes' => 'array',
        'metadata' => 'array',
        'converted_at' => 'datetime',
    ];

    public function getCreatedAtAttribute($value)
    {
        return DateTimeHelper::convertToCompanyTimezone($value)?->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return DateTimeHelper::convertToCompanyTimezone($value)?->toDateTimeString();
    }

    /**
     * Relationship: Admin user assigned to lead
     */
    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relationship: Organization converted into
     */
    public function convertedOrganization()
    {
        return $this->belongsTo(Organization::class, 'converted_to_organization_id');
    }

    /**
     * Append an internal note
     */
    public function addNote(string $author, string $text): void
    {
        $currentNotes = $this->notes ?? [];
        $currentNotes[] = [
            'id' => (string) Str::uuid(),
            'author' => $author,
            'text' => $text,
            'created_at' => now()->toIso8601String(),
        ];

        $this->notes = $currentNotes;
        $this->save();
    }
}
