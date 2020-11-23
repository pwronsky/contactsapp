<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CorrectImportCsvFile implements Rule
{
    private const REQUIRED_FIELDS = ['first_name', 'last_name', 'email', 'phone'];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$value->isValid()) {
            return false;
        }

        $handle = fopen($value, "r");
        $header = fgetcsv($handle);
        fclose($handle);

        if (count($header) !== count(self::REQUIRED_FIELDS)) {
            return false;
        }

        foreach (self::REQUIRED_FIELDS as $value) {
            if (!in_array($value, $header)) {
                return false;
            }
        }

       return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Incorrect file format. File should contain a header with a first_name, last_name, email, phone, which will point position of values';
    }
}
