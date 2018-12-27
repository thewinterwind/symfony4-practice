<?php

namespace App\Validators;

use DateTime;

class DateValidator {

    public function isValidDate(string $date)
    {
        $format = 'Y-m-d';

        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }
}
