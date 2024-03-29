<?php

namespace K310\Helpers;

use Illuminate\Support\Traits\Macroable;

class Luhn
{
    use Macroable;

    /**
     * Returns the given number with luhn algorithm applied.
     *
     * For example 456 becomes 4564.
     *
     * @param  integer $number
     * @return integer
     */
    public static function generate($number)
    {
        $stack = 0;
        $digits = str_split(strrev($number), 1);

        foreach($digits as $key => $value)
        {
            if($key % 2 === 0)
            {
                $value = array_sum(str_split($value * 2, 1));
            }

            $stack += $value;
        }

        $stack %= 10;

        if($stack !== 0)
        {
            $stack -= 10;
        }

        return (int) (implode('', array_reverse($digits)) . abs($stack));
    }

    /**
     * Validates the given number.
     *
     * @param  integer $number
     * @return boolean
     */
    public static function validate($number)
    {
        $original = substr($number, 0, strlen($number) - 1);

        return Luhn::generate($original) === $number;
    }
}