<?php

namespace K310\Helpers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        require_once(__DIR__ . '/helpers.php');
    }

    public function boot()
    {
        $this->addStringHelpers();
        $this->addArrayHelpers();
    }

    //---------------------------------------------------------------------------------
    //-------------------------------------------------------------------------- STRING
    //---------------------------------------------------------------------------------

    protected function addStringHelpers()
    {
        //---------------------------------------------------------------------- upper

        /**
         * Put a string in upper case with accent after trimming it.
         * @todo strtr($string, array("ß" => "SS")) for german.
         * @param  string  $string
         * @param  boolean $preserveAccent Keep accent in upper case if "true" or replace with the same letter witout accent if "false".
         * @return string
         */
        Str::macro('upper', function($string, $preserveAccent)
        {
            $string = trim($string);

            if(!$preserveAccent)
            {
                $string = Str::removeAccent($string);
            }

            $string = strtoupper($string);

            return strtr($string, 'äâàáåãéèëêòóôõöøìíîïùúûüýñçþÿæœðø', 'ÄÂÀÁÅÃÉÈËÊÒÓÔÕÖØÌÍÎÏÙÚÛÜÝÑÇÞÝÆŒÐØ');
        });

        //---------------------------------------------------------------------- remove accent

        /**
         * Remove all accent of a string.
         * This function manage UTF8 string!
         * Replacements:
         * À => A
         * Á => A
         * Â => A
         * Ã => A
         * Ä => A
         * Å => A
         * Ç => C
         * È => E
         * É => E
         * Ê => E
         * Ë => E
         * Ì => I
         * Í => I
         * Î => I
         * Ï => I
         * Ñ => N
         * Ò => O
         * Ó => O
         * Ô => O
         * Õ => O
         * Ö => O
         * Ù => U
         * Ú => U
         * Û => U
         * Ü => U
         * Ý => Y
         * ß => s
         * à => a
         * á => a
         * â => a
         * ã => a
         * ä => a
         * å => a
         * ç => c
         * è => e
         * é => e
         * ê => e
         * ë => e
         * ì => i
         * í => i
         * î => i
         * ï => i
         * ñ => n
         * ò => o
         * ó => o
         * ô => o
         * õ => o
         * ö => o
         * ù => u
         * ú => u
         * û => u
         * ü => u
         * ý => y
         * ÿ => y
         * Ā => A
         * ā => a
         * Ă => A
         * ă => a
         * Ą => A
         * ą => a
         * Ć => C
         * ć => c
         * Ĉ => C
         * ĉ => c
         * Ċ => C
         * ċ => c
         * Č => C
         * č => c
         * Ď => D
         * ď => d
         * Đ => D
         * đ => d
         * Ē => E
         * ē => e
         * Ĕ => E
         * ĕ => e
         * Ė => E
         * ė => e
         * Ę => E
         * ę => e
         * Ě => E
         * ě => e
         * Ĝ => G
         * ĝ => g
         * Ğ => G
         * ğ => g
         * Ġ => G
         * ġ => g
         * Ģ => G
         * ģ => g
         * Ĥ => H
         * ĥ => h
         * Ħ => H
         * ħ => h
         * Ĩ => I
         * ĩ => i
         * Ī => I
         * ī => i
         * Ĭ => I
         * ĭ => i
         * Į => I
         * į => i
         * İ => I
         * ı => i
         * Ĳ => IJ
         * ĳ => ij
         * Ĵ => J
         * ĵ => j
         * Ķ => K
         * ķ => k
         * ĸ => k
         * Ĺ => L
         * ĺ => l
         * Ļ => L
         * ļ => l
         * Ľ => L
         * ľ => l
         * Ŀ => L
         * ŀ => l
         * Ł => L
         * ł => l
         * Ń => N
         * ń => n
         * Ņ => N
         * ņ => n
         * Ň => N
         * ň => n
         * ŉ => N
         * Ŋ => n
         * ŋ => N
         * Ō => O
         * ō => o
         * Ŏ => O
         * ŏ => o
         * Ő => O
         * ő => o
         * Œ => OE
         * œ => oe
         * Ŕ => R
         * ŕ => r
         * Ŗ => R
         * ŗ => r
         * Ř => R
         * ř => r
         * Ś => S
         * ś => s
         * Ŝ => S
         * ŝ => s
         * Ş => S
         * ş => s
         * Š => S
         * š => s
         * Ţ => T
         * ţ => t
         * Ť => T
         * ť => t
         * Ŧ => T
         * ŧ => t
         * Ũ => U
         * ũ => u
         * Ū => U
         * ū => u
         * Ŭ => U
         * ŭ => u
         * Ů => U
         * ů => u
         * Ű => U
         * ű => u
         * Ų => U
         * ų => u
         * Ŵ => W
         * ŵ => w
         * Ŷ => Y
         * ŷ => y
         * Ÿ => Y
         * Ź => Z
         * ź => z
         * Ż => Z
         * ż => z
         * Ž => Z
         * ž => z
         * ſ => s
         * Ø => O
         * ø => o
         * @param  string $string
         * @return string
         */
        Str::macro('removeAccent', function($string)
        {
            if(!preg_match('/[\x80-\xff]/', $string))
            {
                return $string;
            }

            $chars = array(
                // Decompositions for Latin-1 Supplement
                chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
                chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
                chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
                chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
                chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
                chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
                chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
                chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
                chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
                chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
                chr(195).chr(150) => 'O', chr(195).chr(152) => 'O', 
                chr(195).chr(153) => 'U',
                chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
                chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
                chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
                chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
                chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
                chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
                chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
                chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
                chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
                chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
                chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
                chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
                chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
                chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
                chr(195).chr(185) => 'u', chr(195).chr(186) => 'u', 
                chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
                chr(195).chr(189) => 'y', chr(195).chr(191) => 'y',
                // Decompositions for Latin Extended-A
                chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
                chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
                chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
                chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
                chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
                chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
                chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
                chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
                chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
                chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
                chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
                chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
                chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
                chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
                chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
                chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
                chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
                chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
                chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
                chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
                chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
                chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
                chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
                chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
                chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
                chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
                chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
                chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
                chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
                chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
                chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
                chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
                chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
                chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
                chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
                chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
                chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
                chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
                chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
                chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
                chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
                chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
                chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
                chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
                chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
                chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
                chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
                chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
                chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
                chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
                chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
                chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
                chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
                chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
                chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
                chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
                chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
                chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
                chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
                chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
                chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
                chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
                chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
                chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
            );

            $string = strtr($string, $chars);

            return $string;
        });

        //---------------------------------------------------------------------- keyfied

        /**
         * Make a key from a string.
         * To build the key, this function remove all accents, put the string
         * in lower case and remove all non-alpha-numeric character.
         * @param  string $string
         * @return string
         */
        Str::macro('keyfied', function($string)
        {
            $string = Str::removeAccent(trim($string));
            $string = strtolower($string);

            return preg_replace('#[^a-zA-Z0-9]#mi', '', $string);
        });


        /**
         * Optimize string for search.
         * @param  string $string
         * @return string
         */
        Str::macro('searchable', function($string)
        {
            $string = preg_replace("/[^\w\s+$]/u", '', $string);
            $string = preg_replace('/\s+/', ' ', $string);
            $string = Str::removeAccent($string);
            $string = trim($string);

            return strtolower($string);
        });

        //---------------------------------------------------------------------- random

        /**
         * Generate a random string of length $length.
         * @param  integer $length
         * @param  bool $specialCharsToo Add "!@#$%^&*()-=+;:,.?" as possibilities.
         * @return string
         */
        Str::macro('random', function($length = 8, $specialCharsToo = false)
        {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

            if($specialCharsToo)
            {
                $chars .= '!@#$%^&*()-=+;:,.?_';
            }

            return substr(str_shuffle($chars), 0, $length);
        });
    }

    //--------------------------------------------------------------------------------
    //-------------------------------------------------------------------------- ARRAY
    //--------------------------------------------------------------------------------

    protected function addArrayHelpers()
    {
        //---------------------------------------------------------------------- indexBy

        /**
         * Index array $rows by its column $indexColumnName.
         * @param  array $rows
         * @param  string|int $indexColumnName
         * @return array
         */
        Arr::macro('indexBy', function($rows, $indexColumnName)
        {
            if(count($rows) == 0)
            {
                return [];
            }
    
            $indexedRows = [];
    
            foreach($rows AS $row)
            {
                $indexedRows[$row[$indexColumnName]] = $row;
            }
    
            return $indexedRows;
        });

        //---------------------------------------------------------------------- pairBy

        /**
         * Return a key/value array where key column is $keyColumnName
         * and value column $valueColumnName.
         * @param  array $rows
         * @param  string|int $keyColumnName
         * @param  string|int $valueColumnName
         * @return array
         */
        Arr::macro('pairBy', function($rows, $keyColumnName, $valueColumnName)
        {
            $keyValueRows = array();

            foreach($rows AS $row)
            {
                $keyValueRows[$row[$keyColumnName]] = $row[$valueColumnName];
            }
    
            return $keyValueRows;
        });

        //---------------------------------------------------------------------- orderBy

        /**
         * Order a two dimensional associative array.
         * @example
         * Arr::orderBy($array, 'column1NameOrIndex', SORT_DESC, 'column2NameOrIndex', SORT_ASC, ...)
         * @return array Sorted array
         */
        Arr::macro('orderBy', function()
        {
            $args = func_get_args();
            $data = array_shift($args);
    
            foreach($args AS $n => $field)
            {
                if (is_string($field))
                {
                    $tmp = array();
    
                    foreach($data AS $key => $row)
                    {
                        $tmp[$key] = $row[$field];
                    }
    
                    $args[$n] = $tmp;
                }
            }
    
            $args[] = &$data;
            call_user_func_array('array_multisort', $args);
    
            return array_pop($args);
        });

        //---------------------------------------------------------------------- orderSpecific

        /**
         * Order a two dimensional associative array by a specific order defined in an array.
         * @example
         * Arr::orderSpecific($array, 'name', ['Jacques', 'Alain', 'Pierre', 'Jean'])
         * @return array Sorted array
         */
        Arr::macro('orderSpecific', function($array, $key, $orderArray)
        {
            $dict = array_flip($orderArray);

            $positions = array_map(function($elem) use ($dict, $key)
            {
                return $dict[$elem[$key]] ?? INF;
            }, $array);

            array_multisort($positions, $array);

            return $array;
        });

        //---------------------------------------------------------------------- groupBy

        /**
         * Group a table $array by a key.
         * @example
         * A::groupBy($array,'age');
         * IN  : array(
         * 			   array('first_name' => 'George', 'last_name' => 'Clooney', 'age' => 42),
         * 			   array('first_name' => 'Brad',   'last_name' => 'Pitt',    'age' => 42),
         * 			   array('first_name' => 'Bruce',  'last_name' => 'Willis',  'age' => 61)
         * 			  );
         * OUT  : array(
         * 				42 => array(
         * 						    array('first_name' => 'George', 'last_name' => 'Clooney', 'age' => 42),
         * 						    array('first_name' => 'Brad',   'last_name' => 'Pitt',    'age' => 42)
         * 						   )
         * 				),
         * 				61 => array(
         * 						    array('first_name' => 'Bruce', 'last_name' => 'Willis', 'age' => 61)
         * 						   )
         * @param  array $array
         * @param  mixed $iterator String, index or function(value, key)
         * @param  string $index If not null, return only this key for value
         * @return array
         */
        Arr::macro('groupBy', function($array, $iterator = null, $index = null)
        {
            $result = array();
            $array = (array) $array;
    
            foreach($array AS $k => $v)
            {
                $key = (is_callable($iterator)) ? $iterator($v, $k) : $v[$iterator];
    
                if(!array_key_exists($key, $result))
                {
                    $result[$key] = array();
                }
    
                $result[$key][] = (!empty($index)) ? $v[$index] : $v;
            }
    
            return $result;
        });

        //---------------------------------------------------------------------- keepKeys

        /**
         * Return $array with only the specified keys.
         * @example
         * A::keepKeys(array('a' => 1,
         *                   'b' => 2,
         *                   'c' => 3,
         *                   'd' => 4), array('a', 'c'));
         *
         * OUT : array('a' => 1,
         *             'c' => 3);
         * @param  array $array
         * @param  array $array ...
         * @return array
         */
        Arr::macro('keepKeys', function($array, $keysToKeep)
        {
            return array_intersect_key($array, array_flip($keysToKeep));
        });
    }
}