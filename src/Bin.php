<?php

namespace Constellation\Helpers;

use Illuminate\Support\Traits\Macroable;

class Bin
{
    use Macroable;
    
    /**
     * Return all values contained in a binary
     * @param  int 
     * @return array An array of int
     */
    public static function decompose($value)
    {
        $i = 1;
        $values = [];
        $binaryValue = intval($value);

        while($i <= $binaryValue)
        {
            if($binaryValue & $i)
            {
                $values[] = $i;
            }

            $i *= 2;
        }

        return $values;
    }

    /**
     * Determine a binary value from all its component
     * @param  array 
     * @return int
     */
    public static function compose($values)
    {
        $binary = 0;

        if(is_array($values))
        {
            foreach($values as $value)
            {
                $binary += intval($value);
            }
        }

        return $binary;
    }
}