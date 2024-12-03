<?php

namespace App\Rules;

use App\Models\User;
use Exception;
use Illuminate\Contracts\Validation\Rule;

class ValidUserContactNumber implements Rule
{
    protected $userLookup;

    /**
     * Create a new rule instance.
     *
     * @param  bool  $userLookup  Whether to validate by looking up a user.
     */
    public function __construct(bool $userLookup = false)
    {
        $this->userLookup = $userLookup;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->userLookup) {
            // Validate contact number by looking up a user
            $user = User::find($value);

            if (! $user) {
                return false; // User does not exist
            }

            // Construct the phone number in E.164 format
            $phone_number = "+{$user->countryCode}{$user->contact_no}";

        } else {
            // Validate the direct contact number
            if (strpos($value, '+') !== 0) {
                // Add '+' at the beginning if it doesn't exist
                $phone_number = '+' . $value;
            } else {
                $phone_number = $value;
            }
        }

        // Check if the phone number matches E.164 format
        return preg_match('/^\+\d{10,12}$/', $phone_number);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The contact number must be in a valid E.164 format.';
    }
}
