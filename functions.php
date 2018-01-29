<?php

    function loadURL($url, $cookie, $user_agent, $fields=array()) {
        $ch 		= curl_init($url);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt( $ch, CURLOPT_HEADER, 0 );

        if (empty($fields) && count($fields) ==0) {
            curl_setopt( $ch, CURLOPT_HTTPGET, 1 );
        } else {
            $fields_string = "";
            foreach($fields as $key => $value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
            curl_setopt( $ch, CURLOPT_POST, 1 );
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        }

        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Cookie: " . $cookie));
        curl_setopt( $ch, CURLOPT_USERAGENT, $user_agent);
        $output 	= curl_exec($ch);
        curl_close( $ch );
        return $output;
    }

    function slug($string, $replacement = '_') {
        $transliteration = array(
            '/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
            '/Æ|Ǽ/' => 'AE',
            '/Ä/' => 'Ae',
            '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
            '/Ð|Ď|Đ/' => 'D',
            '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
            '/Ĝ|Ğ|Ġ|Ģ|Ґ/' => 'G',
            '/Ĥ|Ħ/' => 'H',
            '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|І/' => 'I',
            '/Ĳ/' => 'IJ',
            '/Ĵ/' => 'J',
            '/Ķ/' => 'K',
            '/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
            '/Ñ|Ń|Ņ|Ň/' => 'N',
            '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
            '/Œ/' => 'OE',
            '/Ö/' => 'Oe',
            '/Ŕ|Ŗ|Ř/' => 'R',
            '/Ś|Ŝ|Ş|Ș|Š/' => 'S',
            '/ẞ/' => 'SS',
            '/Ţ|Ț|Ť|Ŧ/' => 'T',
            '/Þ/' => 'TH',
            '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
            '/Ü/' => 'Ue',
            '/Ŵ/' => 'W',
            '/Ý|Ÿ|Ŷ/' => 'Y',
            '/Є/' => 'Ye',
            '/Ї/' => 'Yi',
            '/Ź|Ż|Ž/' => 'Z',
            '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
            '/ä|æ|ǽ/' => 'ae',
            '/ç|ć|ĉ|ċ|č/' => 'c',
            '/ð|ď|đ/' => 'd',
            '/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
            '/ƒ/' => 'f',
            '/ĝ|ğ|ġ|ģ|ґ/' => 'g',
            '/ĥ|ħ/' => 'h',
            '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|і/' => 'i',
            '/ĳ/' => 'ij',
            '/ĵ/' => 'j',
            '/ķ/' => 'k',
            '/ĺ|ļ|ľ|ŀ|ł/' => 'l',
            '/ñ|ń|ņ|ň|ŉ/' => 'n',
            '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
            '/ö|œ/' => 'oe',
            '/ŕ|ŗ|ř/' => 'r',
            '/ś|ŝ|ş|ș|š|ſ/' => 's',
            '/ß/' => 'ss',
            '/ţ|ț|ť|ŧ/' => 't',
            '/þ/' => 'th',
            '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
            '/ü/' => 'ue',
            '/ŵ/' => 'w',
            '/ý|ÿ|ŷ/' => 'y',
            '/є/' => 'ye',
            '/ї/' => 'yi',
            '/ź|ż|ž/' => 'z',
        );
        
        $quotedReplacement = preg_quote($replacement, '/');

        $merge = array(
            '/[^\s\p{Zs}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
            '/[\s\p{Zs}]+/mu' => $replacement,
            sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
        );

        $map = $transliteration + $merge;
        return preg_replace(array_keys($map), array_values($map), $string);
    }

?>