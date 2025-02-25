<?php

namespace K310\Helpers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Transliterator;

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

        //---------------------------------------------------------------------- cyrillique to latin

        /**
         * Transform a Cyrillic string to Latin (ISO 8859-1).
         * @param  string  $string
         * @return string
         */
        Str::macro('cyrillicToLatin', function($string)
        {
            $transliterator = Transliterator::create('Cyrillic-Latin');

            return $transliterator->transliterate($string);
        });

        //---------------------------------------------------------------------- remove accents

        /**
         * Remove all accents of a string.
         * This function manage UTF8 string!
         * @param  string $string
         * @return string
         */
        Str::macro('removeAccent', function($string)
        {
            if(!preg_match('/[\x80-\xff]/', $string))
            {
                return $string;
            }

            $chars = [
                // Decompositions for Latin-1 Supplement
                chr(195).chr(128) => 'A', // À (U+00C0) replaced by 'A'
                chr(195).chr(129) => 'A', // Á (U+00C1) replaced by 'A'
                chr(195).chr(130) => 'A', // Â (U+00C2) replaced by 'A'
                chr(195).chr(131) => 'A', // Ã (U+00C3) replaced by 'A'
                chr(195).chr(132) => 'A', // Ä (U+00C4) replaced by 'A'
                chr(195).chr(133) => 'A', // Å (U+00C5) replaced by 'A'
                chr(195).chr(135) => 'C', // Ç (U+00C7) replaced by 'C'
                chr(195).chr(136) => 'E', // È (U+00C8) replaced by 'E'
                chr(195).chr(137) => 'E', // É (U+00C9) replaced by 'E'
                chr(195).chr(138) => 'E', // Ê (U+00CA) replaced by 'E'
                chr(195).chr(139) => 'E', // Ë (U+00CB) replaced by 'E'
                chr(195).chr(140) => 'I', // Ì (U+00CC) replaced by 'I'
                chr(195).chr(141) => 'I', // Í (U+00CD) replaced by 'I'
                chr(195).chr(142) => 'I', // Î (U+00CE) replaced by 'I'
                chr(195).chr(143) => 'I', // Ï (U+00CF) replaced by 'I'
                chr(195).chr(145) => 'N', // Ñ (U+00D1) replaced by 'N'
                chr(195).chr(146) => 'O', // Ò (U+00D2) replaced by 'O'
                chr(195).chr(147) => 'O', // Ó (U+00D3) replaced by 'O'
                chr(195).chr(148) => 'O', // Ô (U+00D4) replaced by 'O'
                chr(195).chr(149) => 'O', // Õ (U+00D5) replaced by 'O'
                chr(195).chr(150) => 'O', // Ö (U+00D6) replaced by 'O'
                chr(195).chr(152) => 'O', // Ø (U+00D8) replaced by 'O'
                chr(195).chr(153) => 'U', // Ù (U+00D9) replaced by 'U'
                chr(195).chr(154) => 'U', // Ú (U+00DA) replaced by 'U'
                chr(195).chr(155) => 'U', // Û (U+00DB) replaced by 'U'
                chr(195).chr(156) => 'U', // Ü (U+00DC) replaced by 'U'
                chr(195).chr(157) => 'Y', // Ý (U+00DD) replaced by 'Y'
                chr(195).chr(159) => 's', // ß (U+00DF) replaced by 's'
                chr(195).chr(160) => 'a', // à (U+00E0) replaced by 'a'
                chr(195).chr(161) => 'a', // á (U+00E1) replaced by 'a'
                chr(195).chr(162) => 'a', // â (U+00E2) replaced by 'a'
                chr(195).chr(163) => 'a', // ã (U+00E3) replaced by 'a'
                chr(195).chr(164) => 'a', // ä (U+00E4) replaced by 'a'
                chr(195).chr(165) => 'a', // å (U+00E5) replaced by 'a'
                chr(195).chr(167) => 'c', // ç (U+00E7) replaced by 'c'
                chr(195).chr(168) => 'e', // è (U+00E8) replaced by 'e'
                chr(195).chr(169) => 'e', // é (U+00E9) replaced by 'e'
                chr(195).chr(170) => 'e', // ê (U+00EA) replaced by 'e'
                chr(195).chr(171) => 'e', // ë (U+00EB) replaced by 'e'
                chr(195).chr(172) => 'i', // ì (U+00EC) replaced by 'i'
                chr(195).chr(173) => 'i', // í (U+00ED) replaced by 'i'
                chr(195).chr(174) => 'i', // î (U+00EE) replaced by 'i'
                chr(195).chr(175) => 'i', // ï (U+00EF) replaced by 'i'
                chr(195).chr(177) => 'n', // ñ (U+00F1) replaced by 'n'
                chr(195).chr(178) => 'o', // ò (U+00F2) replaced by 'o'
                chr(195).chr(179) => 'o', // ó (U+00F3) replaced by 'o'
                chr(195).chr(180) => 'o', // ô (U+00F4) replaced by 'o'
                chr(195).chr(181) => 'o', // õ (U+00F5) replaced by 'o'
                chr(195).chr(182) => 'o', // ö (U+00F6) replaced by 'o'
                chr(195).chr(184) => 'o', // ø (U+00F8) replaced by 'o'
                chr(195).chr(185) => 'u', // ù (U+00F9) replaced by 'u'
                chr(195).chr(186) => 'u', // ú (U+00FA) replaced by 'u'
                chr(195).chr(187) => 'u', // û (U+00FB) replaced by 'u'
                chr(195).chr(188) => 'u', // ü (U+00FC) replaced by 'u'
                chr(195).chr(189) => 'y', // ý (U+00FD) replaced by 'y'
                chr(195).chr(191) => 'y', // ÿ (U+00FF) replaced by 'y'
                // Decompositions for Latin Extended-A
                chr(196).chr(128) => 'A', // 'Ä' (U+00C4) replaced by 'A'
                chr(196).chr(129) => 'a', // 'ä' (U+00E4) replaced by 'a'
                chr(196).chr(130) => 'A', // 'Å' (U+00C5) replaced by 'A'
                chr(196).chr(131) => 'a', // 'å' (U+00E5) replaced by 'a'
                chr(196).chr(132) => 'A', // 'Æ' (U+00C6) replaced by 'A'
                chr(196).chr(133) => 'a', // 'æ' (U+00E6) replaced by 'a'
                chr(196).chr(134) => 'C', // 'Ç' (U+00C7) replaced by 'C'
                chr(196).chr(135) => 'c', // 'ç' (U+00E7) replaced by 'c'
                chr(196).chr(136) => 'Č', // 'Č' (U+010C) replaced by 'C'
                chr(196).chr(137) => 'c', // 'č' (U+010D) replaced by 'c'
                chr(196).chr(138) => 'C', // 'Č' (U+010C) replaced by 'C'
                chr(196).chr(139) => 'c', // 'č' (U+010D) replaced by 'c'
                chr(196).chr(140) => 'C', // 'Č' (U+010C) replaced by 'C'
                chr(196).chr(141) => 'c', // 'č' (U+010D) replaced by 'c'
                chr(196).chr(142) => 'D', // 'Ď' (U+010E) replaced by 'D'
                chr(196).chr(143) => 'd', // 'ď' (U+010F) replaced by 'd'
                chr(196).chr(144) => 'D', // 'Đ' (U+0110) replaced by 'D'
                chr(196).chr(145) => 'd', // 'đ' (U+0111) replaced by 'd'
                chr(196).chr(146) => 'E', // 'Ē' (U+0112) replaced by 'E'
                chr(196).chr(147) => 'e', // 'ē' (U+0113) replaced by 'e'
                chr(196).chr(148) => 'E', // 'Ĕ' (U+0114) replaced by 'E'
                chr(196).chr(149) => 'e', // 'ĕ' (U+0115) replaced by 'e'
                chr(196).chr(150) => 'E', // 'Ė' (U+0116) replaced by 'E'
                chr(196).chr(151) => 'e', // 'ė' (U+0117) replaced by 'e'
                chr(196).chr(152) => 'E', // 'Ę' (U+0118) replaced by 'E'
                chr(196).chr(153) => 'e', // 'ę' (U+0119) replaced by 'e'
                chr(196).chr(154) => 'E', // 'Ě' (U+011A) replaced by 'E'
                chr(196).chr(155) => 'e', // 'ě' (U+011B) replaced by 'e'
                chr(196).chr(156) => 'G', // 'Ĝ' (U+011C) replaced by 'G'
                chr(196).chr(157) => 'g', // 'ĝ' (U+011D) replaced by 'g'
                chr(196).chr(158) => 'G', // 'Ğ' (U+011E) replaced by 'G'
                chr(196).chr(159) => 'g', // 'ğ' (U+011F) replaced by 'g'
                chr(196).chr(160) => 'G', // 'Ġ' (U+0120) replaced by 'G'
                chr(196).chr(161) => 'g', // 'ġ' (U+0121) replaced by 'g'
                chr(196).chr(162) => 'G', // 'Ģ' (U+0122) replaced by 'G'
                chr(196).chr(163) => 'g', // 'ģ' (U+0123) replaced by 'g'
                chr(196).chr(164) => 'H', // 'Ĥ' (U+0124) replaced by 'H'
                chr(196).chr(165) => 'h', // 'ĥ' (U+0125) replaced by 'h'
                chr(196).chr(166) => 'H', // 'Ħ' (U+0126) replaced by 'H'
                chr(196).chr(167) => 'h', // 'ħ' (U+0127) replaced by 'h'
                chr(196).chr(168) => 'I', // 'Ĩ' (U+0128) replaced by 'I'
                chr(196).chr(169) => 'i', // 'ĩ' (U+0129) replaced by 'i'
                chr(196).chr(170) => 'I', // 'Ī' (U+012A) replaced by 'I'
                chr(196).chr(171) => 'i', // 'ī' (U+012B) replaced by 'i'
                chr(196).chr(172) => 'I', // 'Ĭ' (U+012C) replaced by 'I'
                chr(196).chr(173) => 'i', // 'ĭ' (U+012D) replaced by 'i'
                chr(196).chr(174) => 'I', // 'Į' (U+012E) replaced by 'I'
                chr(196).chr(175) => 'i', // 'į' (U+012F) replaced by 'i'
                chr(196).chr(176) => 'I', // 'İ' (U+0130) replaced by 'I'
                chr(196).chr(177) => 'i', // 'ı' (U+0131) replaced by 'i'
                chr(196).chr(178) => 'IJ', // 'Ĳ' (U+0132) replaced by 'IJ'
                chr(196).chr(179) => 'ij', // 'ĳ' (U+0133) replaced by 'ij'
                chr(196).chr(180) => 'J', // 'Ĵ' (U+0134) replaced by 'J'
                chr(196).chr(181) => 'j', // 'ĵ' (U+0135) replaced by 'j'
                chr(196).chr(182) => 'K', // 'Ķ' (U+0136) replaced by 'K'
                chr(196).chr(183) => 'k', // 'ķ' (U+0137) replaced by 'k'
                chr(196).chr(184) => 'k', // 'ĸ' (U+0138) replaced by 'k'
                chr(196).chr(185) => 'L', // 'Ĺ' (U+0139) replaced by 'L'
                chr(196).chr(186) => 'l', // 'ĺ' (U+013A) replaced by 'l'
                chr(196).chr(187) => 'L', // 'Ļ' (U+013B) replaced by 'L'
                chr(196).chr(188) => 'l', // 'ļ' (U+013C) replaced by 'l'
                chr(196).chr(189) => 'L', // 'Ľ' (U+013D) replaced by 'L'
                chr(196).chr(190) => 'l', // 'ľ' (U+013E) replaced by 'l'
                chr(196).chr(191) => 'L', // 'Ŀ' (U+013F) replaced by 'L'
                chr(197).chr(128) => 'l', // 'ŀ' (U+0140) replaced by 'l'
                chr(197).chr(129) => 'L', // 'Ł' (U+0141) replaced by 'L'
                chr(197).chr(130) => 'l', // 'ł' (U+0142) replaced by 'l'
                chr(197).chr(131) => 'N', // 'Ń' (U+0143) replaced by 'N'
                chr(197).chr(132) => 'n', // 'ń' (U+0144) replaced by 'n'
                chr(197).chr(133) => 'N', // 'Ņ' (U+0145) replaced by 'N'
                chr(197).chr(134) => 'n', // 'ņ' (U+0146) replaced by 'n'
                chr(197).chr(135) => 'N', // 'Ň' (U+0147) replaced by 'N'
                chr(197).chr(136) => 'n', // 'ň' (U+0148) replaced by 'n'
                chr(197).chr(137) => 'N', // 'ŉ' (U+0149) replaced by 'N'
                chr(197).chr(138) => 'n', // 'Ŋ' (U+014A) replaced by 'n'
                chr(197).chr(139) => 'N', // 'ŋ' (U+014B) replaced by 'N'
                chr(197).chr(140) => 'O', // 'Ō' (U+014C) replaced by 'O'
                chr(197).chr(141) => 'o', // 'ō' (U+014D) replaced by 'o'
                chr(197).chr(142) => 'O', // 'Ŏ' (U+014E) replaced by 'O'
                chr(197).chr(143) => 'o', // 'ŏ' (U+014F) replaced by 'o'
                chr(197).chr(144) => 'O', // 'Ő' (U+0150) replaced by 'O'
                chr(197).chr(145) => 'o', // 'ő' (U+0151) replaced by 'o'
                chr(197).chr(146) => 'OE', // 'Œ' (U+0152) replaced by 'OE'
                chr(197).chr(147) => 'oe', // 'œ' (U+0153) replaced by 'oe'
                chr(197).chr(148) => 'R', // 'Ŕ' (U+0154) replaced by 'R'
                chr(197).chr(149) => 'r', // 'ŕ' (U+0155) replaced by 'r'
                chr(197).chr(150) => 'R', // 'Ŗ' (U+0156) replaced by 'R'
                chr(197).chr(151) => 'r', // 'ŗ' (U+0157) replaced by 'r'
                chr(197).chr(152) => 'R', // 'Ř' (U+0158) replaced by 'R'
                chr(197).chr(153) => 'r', // 'ř' (U+0159) replaced by 'r'
                chr(197).chr(154) => 'S', // 'Ś' (U+015A) replaced by 'S'
                chr(197).chr(155) => 's', // 'ś' (U+015B) replaced by 's'
                chr(197).chr(156) => 'S', // 'Ŝ' (U+015C) replaced by 'S'
                chr(197).chr(157) => 's', // 'ŝ' (U+015D) replaced by 's'
                chr(197).chr(158) => 'S', // 'Ş' (U+015E) replaced by 'S'
                chr(197).chr(159) => 's', // 'ş' (U+015F) replaced by 's'
                chr(197).chr(160) => 'S', // 'Š' (U+0160) replaced by 'S'
                chr(197).chr(161) => 's', // 'š' (U+0161) replaced by 's'
                chr(197).chr(162) => 'T', // 'Ţ' (U+0162) replaced by 'T'
                chr(197).chr(163) => 't', // 'ţ' (U+0163) replaced by 't'
                chr(197).chr(164) => 'T', // 'Ť' (U+0164) replaced by 'T'
                chr(197).chr(165) => 't', // 'ť' (U+0165) replaced by 't'
                chr(197).chr(166) => 'T', // 'Ŧ' (U+0166) replaced by 'T'
                chr(197).chr(167) => 't', // 'ŧ' (U+0167) replaced by 't'
                chr(197).chr(168) => 'U', // 'Ũ' (U+0168) replaced by 'U'
                chr(197).chr(169) => 'u', // 'ũ' (U+0169) replaced by 'u'
                chr(197).chr(170) => 'U', // 'Ū' (U+016A) replaced by 'U'
                chr(197).chr(171) => 'u', // 'ū' (U+016B) replaced by 'u'
                chr(197).chr(172) => 'U', // 'Ŭ' (U+016C) replaced by 'U'
                chr(197).chr(173) => 'u', // 'ŭ' (U+016D) replaced by 'u'
                chr(197).chr(174) => 'U', // 'Ů' (U+016E) replaced by 'U'
                chr(197).chr(175) => 'u', // 'ů' (U+016F) replaced by 'u'
                chr(197).chr(176) => 'U', // 'Ű' (U+0170) replaced by 'U'
                chr(197).chr(177) => 'u', // 'ű' (U+0171) replaced by 'u'
                chr(197).chr(178) => 'U', // 'Ų' (U+0172) replaced by 'U'
                chr(197).chr(179) => 'u', // 'ų' (U+0173) replaced by 'u'
                chr(197).chr(180) => 'W', // 'Ŵ' (U+0174) replaced by 'W'
                chr(197).chr(181) => 'w', // 'ŵ' (U+0175) replaced by 'w'
                chr(197).chr(182) => 'Y', // 'Ŷ' (U+0176) replaced by 'Y'
                chr(197).chr(183) => 'y', // 'ŷ' (U+0177) replaced by 'y'
                chr(197).chr(184) => 'Y', // 'Ÿ' (U+0178) replaced by 'Y'
                chr(197).chr(185) => 'Z', // 'Ź' (U+0179) replaced by 'Z'
                chr(197).chr(186) => 'z', // 'ź' (U+017A) replaced by 'z'
                chr(197).chr(187) => 'Z', // 'Ż' (U+017B) replaced by 'Z'
                chr(197).chr(188) => 'z', // 'ż' (U+017C) replaced by 'z'
                chr(197).chr(189) => 'Z', // 'Ž' (U+017D) replaced by 'Z'
                chr(197).chr(190) => 'z', // 'ž' (U+017E) replaced by 'z'
                chr(197).chr(191) => 's', // 'ſ' (U+017F) replaced by 's'
            ];

            $string = strtr($string, $chars);

            return $string;
        });

        //---------------------------------------------------------------------- remove non-Latin1 (ISO 8859-1) accents

        /**
         * Remove non-Latin1 (ISO 8859-1) accents of a string.
         * This function manage UTF8 string!
         * @param  string $string
         * @return string
         */
        Str::macro('removeNonLatin1Accent', function($string)
        {
            if(!preg_match('/[\x80-\xff]/', $string))
            {
                return $string;
            }

            $chars = [
                // Decompositions for Latin Extended-A
                chr(196).chr(128) => 'A', // 'Ä' (U+00C4) replaced by 'A'
                chr(196).chr(129) => 'a', // 'ä' (U+00E4) replaced by 'a'
                chr(196).chr(130) => 'A', // 'Å' (U+00C5) replaced by 'A'
                chr(196).chr(131) => 'a', // 'å' (U+00E5) replaced by 'a'
                chr(196).chr(132) => 'A', // 'Æ' (U+00C6) replaced by 'A'
                chr(196).chr(133) => 'a', // 'æ' (U+00E6) replaced by 'a'
                chr(196).chr(134) => 'C', // 'Ç' (U+00C7) replaced by 'C'
                chr(196).chr(135) => 'c', // 'ç' (U+00E7) replaced by 'c'
                chr(196).chr(136) => 'Č', // 'Č' (U+010C) replaced by 'C'
                chr(196).chr(137) => 'c', // 'č' (U+010D) replaced by 'c'
                chr(196).chr(138) => 'C', // 'Č' (U+010C) replaced by 'C'
                chr(196).chr(139) => 'c', // 'č' (U+010D) replaced by 'c'
                chr(196).chr(140) => 'C', // 'Č' (U+010C) replaced by 'C'
                chr(196).chr(141) => 'c', // 'č' (U+010D) replaced by 'c'
                chr(196).chr(142) => 'D', // 'Ď' (U+010E) replaced by 'D'
                chr(196).chr(143) => 'd', // 'ď' (U+010F) replaced by 'd'
                chr(196).chr(144) => 'D', // 'Đ' (U+0110) replaced by 'D'
                chr(196).chr(145) => 'd', // 'đ' (U+0111) replaced by 'd'
                chr(196).chr(146) => 'E', // 'Ē' (U+0112) replaced by 'E'
                chr(196).chr(147) => 'e', // 'ē' (U+0113) replaced by 'e'
                chr(196).chr(148) => 'E', // 'Ĕ' (U+0114) replaced by 'E'
                chr(196).chr(149) => 'e', // 'ĕ' (U+0115) replaced by 'e'
                chr(196).chr(150) => 'E', // 'Ė' (U+0116) replaced by 'E'
                chr(196).chr(151) => 'e', // 'ė' (U+0117) replaced by 'e'
                chr(196).chr(152) => 'E', // 'Ę' (U+0118) replaced by 'E'
                chr(196).chr(153) => 'e', // 'ę' (U+0119) replaced by 'e'
                chr(196).chr(154) => 'E', // 'Ě' (U+011A) replaced by 'E'
                chr(196).chr(155) => 'e', // 'ě' (U+011B) replaced by 'e'
                chr(196).chr(156) => 'G', // 'Ĝ' (U+011C) replaced by 'G'
                chr(196).chr(157) => 'g', // 'ĝ' (U+011D) replaced by 'g'
                chr(196).chr(158) => 'G', // 'Ğ' (U+011E) replaced by 'G'
                chr(196).chr(159) => 'g', // 'ğ' (U+011F) replaced by 'g'
                chr(196).chr(160) => 'G', // 'Ġ' (U+0120) replaced by 'G'
                chr(196).chr(161) => 'g', // 'ġ' (U+0121) replaced by 'g'
                chr(196).chr(162) => 'G', // 'Ģ' (U+0122) replaced by 'G'
                chr(196).chr(163) => 'g', // 'ģ' (U+0123) replaced by 'g'
                chr(196).chr(164) => 'H', // 'Ĥ' (U+0124) replaced by 'H'
                chr(196).chr(165) => 'h', // 'ĥ' (U+0125) replaced by 'h'
                chr(196).chr(166) => 'H', // 'Ħ' (U+0126) replaced by 'H'
                chr(196).chr(167) => 'h', // 'ħ' (U+0127) replaced by 'h'
                chr(196).chr(168) => 'I', // 'Ĩ' (U+0128) replaced by 'I'
                chr(196).chr(169) => 'i', // 'ĩ' (U+0129) replaced by 'i'
                chr(196).chr(170) => 'I', // 'Ī' (U+012A) replaced by 'I'
                chr(196).chr(171) => 'i', // 'ī' (U+012B) replaced by 'i'
                chr(196).chr(172) => 'I', // 'Ĭ' (U+012C) replaced by 'I'
                chr(196).chr(173) => 'i', // 'ĭ' (U+012D) replaced by 'i'
                chr(196).chr(174) => 'I', // 'Į' (U+012E) replaced by 'I'
                chr(196).chr(175) => 'i', // 'į' (U+012F) replaced by 'i'
                chr(196).chr(176) => 'I', // 'İ' (U+0130) replaced by 'I'
                chr(196).chr(177) => 'i', // 'ı' (U+0131) replaced by 'i'
                chr(196).chr(178) => 'IJ', // 'Ĳ' (U+0132) replaced by 'IJ'
                chr(196).chr(179) => 'ij', // 'ĳ' (U+0133) replaced by 'ij'
                chr(196).chr(180) => 'J', // 'Ĵ' (U+0134) replaced by 'J'
                chr(196).chr(181) => 'j', // 'ĵ' (U+0135) replaced by 'j'
                chr(196).chr(182) => 'K', // 'Ķ' (U+0136) replaced by 'K'
                chr(196).chr(183) => 'k', // 'ķ' (U+0137) replaced by 'k'
                chr(196).chr(184) => 'k', // 'ĸ' (U+0138) replaced by 'k'
                chr(196).chr(185) => 'L', // 'Ĺ' (U+0139) replaced by 'L'
                chr(196).chr(186) => 'l', // 'ĺ' (U+013A) replaced by 'l'
                chr(196).chr(187) => 'L', // 'Ļ' (U+013B) replaced by 'L'
                chr(196).chr(188) => 'l', // 'ļ' (U+013C) replaced by 'l'
                chr(196).chr(189) => 'L', // 'Ľ' (U+013D) replaced by 'L'
                chr(196).chr(190) => 'l', // 'ľ' (U+013E) replaced by 'l'
                chr(196).chr(191) => 'L', // 'Ŀ' (U+013F) replaced by 'L'
                chr(197).chr(128) => 'l', // 'ŀ' (U+0140) replaced by 'l'
                chr(197).chr(129) => 'L', // 'Ł' (U+0141) replaced by 'L'
                chr(197).chr(130) => 'l', // 'ł' (U+0142) replaced by 'l'
                chr(197).chr(131) => 'N', // 'Ń' (U+0143) replaced by 'N'
                chr(197).chr(132) => 'n', // 'ń' (U+0144) replaced by 'n'
                chr(197).chr(133) => 'N', // 'Ņ' (U+0145) replaced by 'N'
                chr(197).chr(134) => 'n', // 'ņ' (U+0146) replaced by 'n'
                chr(197).chr(135) => 'N', // 'Ň' (U+0147) replaced by 'N'
                chr(197).chr(136) => 'n', // 'ň' (U+0148) replaced by 'n'
                chr(197).chr(137) => 'N', // 'ŉ' (U+0149) replaced by 'N'
                chr(197).chr(138) => 'n', // 'Ŋ' (U+014A) replaced by 'n'
                chr(197).chr(139) => 'N', // 'ŋ' (U+014B) replaced by 'N'
                chr(197).chr(140) => 'O', // 'Ō' (U+014C) replaced by 'O'
                chr(197).chr(141) => 'o', // 'ō' (U+014D) replaced by 'o'
                chr(197).chr(142) => 'O', // 'Ŏ' (U+014E) replaced by 'O'
                chr(197).chr(143) => 'o', // 'ŏ' (U+014F) replaced by 'o'
                chr(197).chr(144) => 'O', // 'Ő' (U+0150) replaced by 'O'
                chr(197).chr(145) => 'o', // 'ő' (U+0151) replaced by 'o'
                chr(197).chr(146) => 'OE', // 'Œ' (U+0152) replaced by 'OE'
                chr(197).chr(147) => 'oe', // 'œ' (U+0153) replaced by 'oe'
                chr(197).chr(148) => 'R', // 'Ŕ' (U+0154) replaced by 'R'
                chr(197).chr(149) => 'r', // 'ŕ' (U+0155) replaced by 'r'
                chr(197).chr(150) => 'R', // 'Ŗ' (U+0156) replaced by 'R'
                chr(197).chr(151) => 'r', // 'ŗ' (U+0157) replaced by 'r'
                chr(197).chr(152) => 'R', // 'Ř' (U+0158) replaced by 'R'
                chr(197).chr(153) => 'r', // 'ř' (U+0159) replaced by 'r'
                chr(197).chr(154) => 'S', // 'Ś' (U+015A) replaced by 'S'
                chr(197).chr(155) => 's', // 'ś' (U+015B) replaced by 's'
                chr(197).chr(156) => 'S', // 'Ŝ' (U+015C) replaced by 'S'
                chr(197).chr(157) => 's', // 'ŝ' (U+015D) replaced by 's'
                chr(197).chr(158) => 'S', // 'Ş' (U+015E) replaced by 'S'
                chr(197).chr(159) => 's', // 'ş' (U+015F) replaced by 's'
                chr(197).chr(160) => 'S', // 'Š' (U+0160) replaced by 'S'
                chr(197).chr(161) => 's', // 'š' (U+0161) replaced by 's'
                chr(197).chr(162) => 'T', // 'Ţ' (U+0162) replaced by 'T'
                chr(197).chr(163) => 't', // 'ţ' (U+0163) replaced by 't'
                chr(197).chr(164) => 'T', // 'Ť' (U+0164) replaced by 'T'
                chr(197).chr(165) => 't', // 'ť' (U+0165) replaced by 't'
                chr(197).chr(166) => 'T', // 'Ŧ' (U+0166) replaced by 'T'
                chr(197).chr(167) => 't', // 'ŧ' (U+0167) replaced by 't'
                chr(197).chr(168) => 'U', // 'Ũ' (U+0168) replaced by 'U'
                chr(197).chr(169) => 'u', // 'ũ' (U+0169) replaced by 'u'
                chr(197).chr(170) => 'U', // 'Ū' (U+016A) replaced by 'U'
                chr(197).chr(171) => 'u', // 'ū' (U+016B) replaced by 'u'
                chr(197).chr(172) => 'U', // 'Ŭ' (U+016C) replaced by 'U'
                chr(197).chr(173) => 'u', // 'ŭ' (U+016D) replaced by 'u'
                chr(197).chr(174) => 'U', // 'Ů' (U+016E) replaced by 'U'
                chr(197).chr(175) => 'u', // 'ů' (U+016F) replaced by 'u'
                chr(197).chr(176) => 'U', // 'Ű' (U+0170) replaced by 'U'
                chr(197).chr(177) => 'u', // 'ű' (U+0171) replaced by 'u'
                chr(197).chr(178) => 'U', // 'Ų' (U+0172) replaced by 'U'
                chr(197).chr(179) => 'u', // 'ų' (U+0173) replaced by 'u'
                chr(197).chr(180) => 'W', // 'Ŵ' (U+0174) replaced by 'W'
                chr(197).chr(181) => 'w', // 'ŵ' (U+0175) replaced by 'w'
                chr(197).chr(182) => 'Y', // 'Ŷ' (U+0176) replaced by 'Y'
                chr(197).chr(183) => 'y', // 'ŷ' (U+0177) replaced by 'y'
                chr(197).chr(184) => 'Y', // 'Ÿ' (U+0178) replaced by 'Y'
                chr(197).chr(185) => 'Z', // 'Ź' (U+0179) replaced by 'Z'
                chr(197).chr(186) => 'z', // 'ź' (U+017A) replaced by 'z'
                chr(197).chr(187) => 'Z', // 'Ż' (U+017B) replaced by 'Z'
                chr(197).chr(188) => 'z', // 'ż' (U+017C) replaced by 'z'
                chr(197).chr(189) => 'Z', // 'Ž' (U+017D) replaced by 'Z'
                chr(197).chr(190) => 'z', // 'ž' (U+017E) replaced by 'z'
                chr(197).chr(191) => 's', // 'ſ' (U+017F) replaced by 's'
            ];

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

        //---------------------------------------------------------------------- searchable

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