<?php

namespace App\Helpers;

use App\Models\Organization;
use App\Models\Setting;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\Log;

class DateTimeHelper
{
    /**
     * Primary default timezone for the complete application.
     */
    public const DEFAULT_TIMEZONE = 'Asia/Kolkata';

    /**
     * In-memory cache for organization timezones within request lifecycle.
     *
     * @var array<int|string, string>
     */
    protected static array $orgTzCache = [];

    /**
     * In-memory cache for company/platform timezone.
     */
    protected static ?string $companyTzCache = null;

    /**
     * Validate whether a string is a valid IANA timezone identifier.
     */
    public static function isValidTimezone(?string $tz): bool
    {
        if (empty($tz) || !is_string($tz)) {
            return false;
        }

        static $validIdentifiers = null;
        if ($validIdentifiers === null) {
            $validIdentifiers = array_flip(timezone_identifiers_list());
        }

        return isset($validIdentifiers[$tz]);
    }

    /**
     * Resolve effective timezone following hierarchy:
     * User Timezone -> Organization Timezone -> System Setting -> App Config -> Asia/Kolkata.
     */
    public static function getOrganizationTimezone($organization = null): string
    {
        // 1. Check if user-specific timezone is defined in user meta (if user authenticated)
        if (auth()->check()) {
            $user = auth()->user();
            if (!empty($user->meta)) {
                $userMeta = is_array($user->meta) ? $user->meta : json_decode($user->meta, true);
                if (!empty($userMeta['timezone']) && self::isValidTimezone($userMeta['timezone'])) {
                    return $userMeta['timezone'];
                }
            }
        }

        // 2. Resolve organization
        $orgId = null;
        $orgModel = null;

        if ($organization instanceof Organization) {
            $orgModel = $organization;
            $orgId = $organization->id;
        } elseif (is_numeric($organization) || is_string($organization)) {
            $orgId = $organization;
        } else {
            $orgId = session()->get('current_organization');
        }

        if ($orgId && isset(self::$orgTzCache[$orgId])) {
            return self::$orgTzCache[$orgId];
        }

        if ($orgId && !$orgModel) {
            $orgModel = Organization::find($orgId);
        }

        if ($orgModel) {
            // Check direct column
            if (!empty($orgModel->timezone) && self::isValidTimezone($orgModel->timezone)) {
                if ($orgId) {
                    self::$orgTzCache[$orgId] = $orgModel->timezone;
                }
                return $orgModel->timezone;
            }

            // Check metadata json
            if (!empty($orgModel->metadata)) {
                $metadata = is_array($orgModel->metadata)
                    ? $orgModel->metadata
                    : json_decode($orgModel->metadata, true);

                if (!empty($metadata['timezone'])) {
                    if (self::isValidTimezone($metadata['timezone'])) {
                        if ($orgId) {
                            self::$orgTzCache[$orgId] = $metadata['timezone'];
                        }
                        return $metadata['timezone'];
                    } else {
                        Log::warning("Organization ID {$orgModel->id} has invalid timezone: '{$metadata['timezone']}'. Falling back to default.");
                    }
                }
            }
        }

        // 3. Fallback to system-level setting
        $systemTz = self::getCompanyTimezone();
        if ($orgId) {
            self::$orgTzCache[$orgId] = $systemTz;
        }

        return $systemTz;
    }

    /**
     * Get platform/company timezone for Admin Panel and system operations.
     */
    public static function getCompanyTimezone(): string
    {
        if (self::$companyTzCache !== null) {
            return self::$companyTzCache;
        }

        $systemSetting = Setting::where('key', 'timezone')->value('value');
        if (!empty($systemSetting) && self::isValidTimezone($systemSetting)) {
            self::$companyTzCache = $systemSetting;
            return self::$companyTzCache;
        }

        $appTz = config('app.timezone');
        if (!empty($appTz) && self::isValidTimezone($appTz)) {
            self::$companyTzCache = $appTz;
            return self::$companyTzCache;
        }

        self::$companyTzCache = self::DEFAULT_TIMEZONE;
        return self::$companyTzCache;
    }

    /**
     * Return human-friendly display label for a timezone.
     */
    public static function getTimezoneDisplay(?string $tz = null): string
    {
        $timezone = $tz ?: self::getOrganizationTimezone();

        if ($timezone === self::DEFAULT_TIMEZONE) {
            return 'India Standard Time (IST, UTC+05:30)';
        }

        try {
            $tzObj = new DateTimeZone($timezone);
            $offset = $tzObj->getOffset(new \DateTime('now', new DateTimeZone('UTC')));
            $hours = intdiv($offset, 3600);
            $minutes = abs(intdiv($offset % 3600, 60));
            $sign = $offset >= 0 ? '+' : '-';
            $offsetStr = sprintf('UTC%s%02d:%02d', $sign, abs($hours), $minutes);
            return sprintf('%s (%s)', str_replace('_', ' ', $timezone), $offsetStr);
        } catch (\Exception $e) {
            return $timezone;
        }
    }

    /**
     * Convert timestamp to organization/user timezone.
     */
    public static function convertToOrganizationTimezone($date, $organization = null)
    {
        if (empty($date)) {
            return null;
        }

        $timezone = self::getOrganizationTimezone($organization);

        try {
            if ($date instanceof Carbon) {
                return $date->copy()->setTimezone($timezone);
            }
            return Carbon::parse($date, 'UTC')->setTimezone($timezone);
        } catch (\Exception $e) {
            Log::warning("Failed to convert date to timezone {$timezone}: " . $e->getMessage());
            return Carbon::parse($date);
        }
    }

    /**
     * Convert timestamp to company/platform timezone for Admin views.
     */
    public static function convertToCompanyTimezone($date)
    {
        if (empty($date)) {
            return null;
        }

        $timezone = self::getCompanyTimezone();

        try {
            if ($date instanceof Carbon) {
                return $date->copy()->setTimezone($timezone);
            }
            return Carbon::parse($date, 'UTC')->setTimezone($timezone);
        } catch (\Exception $e) {
            return Carbon::parse($date);
        }
    }

    /**
     * Interpret a local datetime string in organization timezone and return UTC Carbon instance.
     */
    public static function parseLocalToUtc($dateTimeString, ?string $timezone = null): Carbon
    {
        $tz = ($timezone && self::isValidTimezone($timezone)) ? $timezone : self::getOrganizationTimezone();

        try {
            return Carbon::parse($dateTimeString, $tz)->setTimezone('UTC');
        } catch (\Exception $e) {
            Log::warning("DateTimeHelper: parseLocalToUtc failed for {$dateTimeString} with tz {$tz}: " . $e->getMessage());
            return Carbon::parse($dateTimeString)->setTimezone('UTC');
        }
    }

    /**
     * Calculate start and end bounds of "today" in local timezone, with UTC bounds for DB queries.
     */
    public static function getTodayRange(?string $timezone = null): array
    {
        $tz = ($timezone && self::isValidTimezone($timezone)) ? $timezone : self::getOrganizationTimezone();
        $startLocal = Carbon::today($tz)->startOfDay();
        $endLocal = Carbon::today($tz)->endOfDay();

        return [
            'timezone' => $tz,
            'start_local' => $startLocal,
            'end_local' => $endLocal,
            'start_utc' => $startLocal->copy()->setTimezone('UTC'),
            'end_utc' => $endLocal->copy()->setTimezone('UTC'),
        ];
    }

    /**
     * Format a datetime string using system date and time settings in organization timezone.
     */
    public static function formatDate(?string $dateTimeString, $organization = null): string
    {
        if (empty($dateTimeString)) {
            return '';
        }

        static $dateFormat = null;
        static $timeFormat = null;

        if ($dateFormat === null) {
            $dateFormat = Setting::where('key', 'date_format')->value('value') ?: 'Y-m-d';
            $timeFormat = Setting::where('key', 'time_format')->value('value') ?: 'H:i:s';
        }

        try {
            $localized = self::convertToOrganizationTimezone($dateTimeString, $organization);
            return $localized ? $localized->format($dateFormat . ' ' . $timeFormat) : $dateTimeString;
        } catch (\Exception $e) {
            return $dateTimeString;
        }
    }

    /**
     * Format without hours, minutes, and seconds in organization timezone.
     */
    public static function formatDateWithoutHours($date, $organization = null): string
    {
        if (empty($date)) {
            return '';
        }

        try {
            $localized = self::convertToOrganizationTimezone($date, $organization);
            return $localized ? $localized->format('d M Y') : '';
        } catch (\Exception $e) {
            if (is_string($date)) {
                $date = Carbon::parse($date);
            }
            return $date instanceof Carbon ? $date->format('d M Y') : '';
        }
    }

    /**
     * Clear runtime timezone caches (useful in testing or tenant switches).
     */
    public static function clearCache(): void
    {
        self::$orgTzCache = [];
        self::$companyTzCache = null;
    }
}
