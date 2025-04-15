<?php

namespace App\Rules;

use Careminate\Http\Validations\Contracts\Rules\RuleInterface;

class UserRule implements RuleInterface
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    public function passes(string $attribute, $value): bool
    {
        // TODO: Add rule logic
        return true;
    }

    /**
     * Get the error message for the validation rule.
     *
     * @param  string  $attribute
     * @return string
     */
    public function message(string $attribute): string
    {
        return "The {$attribute} field is invalid.";
    }
}
