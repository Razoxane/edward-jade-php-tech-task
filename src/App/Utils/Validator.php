<?php
namespace App\Utils;

use \InvalidArgumentException;

class Validator
{
    /**
     * Validate a named date string
     *
     * @param      string                     $date   The date
     * @param      string                     $name   The name of the variable
     *
     * @throws     \InvalidArgumentException  Invalid format or invalid value, with the name included for clarity
     *
     * @return     string                     The original date value
     */
    public static function validateDate(string $date, string $name)
    {
        $date_array = explode('-', $date);

        if (3 !== count($date_array)) {
            throw new InvalidArgumentException("Value provided for $name is an invalid format. YYYY-MM-DD required.");
        }

        if (false === checkdate($date_array[1], $date_array[2], $date_array[0])) {
            throw new InvalidArgumentException("Value provided for $name is an invalid value. Valid date in the format of YYYY-MM-DD required.");
        }

        return $date;
    }
}
