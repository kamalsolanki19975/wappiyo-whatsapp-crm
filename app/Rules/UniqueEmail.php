<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class UniqueEmail implements Rule
{
    protected $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }
    
    public function passes($attribute, $value)
    {
        $normalized = strtolower(trim((string) $value));

        // Check if normalized email already exists in the users table
        $query = User::whereRaw('LOWER(email) = ?', [$normalized])->whereNull('deleted_at');

        // Exclude the user with the specified ID
        if ($this->ignoreId !== null) {
            $query->where('id', '!=', $this->ignoreId);
        }

        return !$query->exists();
    }

    public function message()
    {
        return __('An account with this email already exists. Please log in or use a different email address.');
    }
}
