<?php

if(!function_exists('cast')) 
{
	/**
     * Cast $value with $type.
	 *
     * @param  string  	$value
     * @param  string  	$type
     * @return mixed
     */
	function cast($value, $type)
	{
        if(!empty($type))
        {
            switch($type)
            {
                case 'json':
                    $value = json_decode($value, true);
                break;
                
                case 'float':
                case 'double':
                    settype($value, 'float');
                break;
                
                case 'int':
                case 'integer':
                    settype($value, 'integer');
                break;
                
                case 'bool':
                case 'boolean':
                    settype($value, 'boolean');
                break;
                
                case 'string':
                    settype($value, 'string');
                break;
            }
		}
		
        return $value;
	}
}

if(!function_exists('inEu')) 
{
	/**
     * Is a country in Euopean Union ?
	 *
     * @param  string  	$countryCode
     * @param  array  	$additionalCountryCodes
     * @return boolean
     */
	function inEu($countryCode, $additionalCountryCodes = [])
	{
        $euCountryCodes = [
            'BE',
            'BG',
            'CZ',
            'DK',
            'DE',
            'EE',
            'IE',
            'EL',
            'ES',
            'FR',
            'HR',
            'IT',
            'CY',
            'LV',
            'LT',
            'LU',
            'HU',
            'MT',
            'NL',
            'AT',
            'PL',
            'PT',
            'RO',
            'SI',
            'SK',
            'FI',
            'SE'
        ];

        return in_array($countryCode, array_merge($euCountryCodes, $additionalCountryCodes));
	}
}
	
if(!function_exists('asprintf')) 
{
	/**
	 * Replace placeholders in a string.
	 * Example:
	 * IN 	: asprintf("My name is :name and I have :age years old.", array(':name' => 'John', ':age' => 42));
	 * OUT 	: "My name is John and I have 42 years old."
	 * @param  string $str  String
	 * @param  array  $vars Placeholders (key => value)
	 * @param  string $char Placeholders prefix (optional | default ":")
	 * @return string
	 */
	function asprintf($str, $vars = array(), $char = ':')
	{
		if(count($vars) > 0)
		{
			foreach($vars AS $k => $v)
			{
				$str = str_replace($char . $k, $v, $str);
			}
		}

		return $str;
	}
}