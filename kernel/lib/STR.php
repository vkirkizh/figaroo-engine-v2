<?php

define('FIGAROO_STRINGS', function_exists("mb_strlen") ? 'MB' : (function_exists("iconv_strlen") ? 'Iconv' : 'PHP'));

switch (FIGAROO_STRINGS) {
	case 'MB':
		mb_internal_encoding('UTF-8');
		mb_http_output('UTF-8');
	break;
	case 'Iconv':
		if (PHP_VERSION_ID < 50600) {
			iconv_set_encoding('input_encoding', 'UTF-8');
			iconv_set_encoding('output_encoding', 'UTF-8');
			iconv_set_encoding('internal_encoding', 'UTF-8');
		} else {
			ini_set('default_charset', 'UTF-8');
		}
	break;
}

class STR {
	public static function length($str) {
		$str = (string)$str;
		if (FIGAROO_STRINGS == 'MB') return mb_strlen($str, 'utf-8');
		return strlen(utf8_decode($str));
	}
	public static function substr($str, $offset, $length = null) {
		$str = (string)$str;
	    if (FIGAROO_STRINGS == 'MB') return mb_substr($str, $offset, $length, 'utf-8');
	    if (FIGAROO_STRINGS == 'Iconv') return iconv_substr($str, $offset, $length, 'utf-8');
	    if (!is_array($a = self::str_split($str))) return false;
	    if ($length !== null) $a = array_slice($a, $offset, $length);
	    else $a = array_slice($a, $offset);
	    return implode('', $a);
	}
	public static function strpos($haystack, $needle, $offset = null) {
	    if ($offset === null or $offset < 0) $offset = 0;
	    if (FIGAROO_STRINGS == 'MB') return mb_strpos($haystack, $needle, $offset, 'utf-8');
	    if (FIGAROO_STRINGS == 'Iconv') return iconv_strpos($haystack, $needle, $offset, 'utf-8');
	    $byte_pos = $offset;
	    do if (($byte_pos = strpos($haystack, $needle, $byte_pos)) === false) return false;
	    while (($char_pos = self::length(substr($haystack, 0, $byte_pos++))) < $offset);
	    return $char_pos;
	}
	public static function stristr($str, $search) {
		$str = (string)$str;
	    if (strlen($search) == 0) return $str;
	    $lstr = self::toLower($str);
	    $lsearch = self::toLower($search);
	    preg_match('/^(.*)'.preg_quote($lsearch).'/us',$lstr, $matches);
	    if (count($matches) == 2) return substr($str, strlen($matches[1]));
	    return false;
	}
	public static function translit($str) {
		$str = (string)$str;
		$str = preg_replace("/[^A-Za-zА-ЯЁа-яё0-9_\\- ]+/uis", "", $str);
		$repl = array(
			'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y',
			'к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f',
			'х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'shch','ъ'=>'','ы'=>'i','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
			' '=>'_',
			'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'Yo','Ж'=>'Zh','З'=>'Z','И'=>'I','Й'=>'Y',
			'К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F',
			'Х'=>'H','Ц'=>'Ts','Ч'=>'CH','Ш'=>'Sh','Щ'=>'Shch','Ъ'=>'','Ы'=>'I','Ь'=>'','Э'=>'E','Ю'=>'Yu','Я'=>'Ya',
		);
		foreach ($repl as $rus => $eng)
			$str = str_replace($rus, $eng, $str);
		return $str;
	}
	public static function get_rnd_iv($iv_len) {
	    $iv = '';
	    while ($iv_len-- > 0)
	        $iv .= chr(mt_rand() & 0xFF);
	    return $iv;
	}
	public static function ucfirst($str) {
		$a = self::substr($str, 0, 1);
		$b = self::substr($str, 1, self::length($str) - 1);
		return self::toUpper($a) . $b;
	}
	public static function toLower($string) {
		$string = (string)$string;
	    static $UTF8_UPPER_TO_LOWER = NULL;
	    if ( is_null($UTF8_UPPER_TO_LOWER) ) {
	        $UTF8_UPPER_TO_LOWER = array(
	    0x0041=>0x0061, 0x03A6=>0x03C6, 0x0162=>0x0163, 0x00C5=>0x00E5, 0x0042=>0x0062,
	    0x0139=>0x013A, 0x00C1=>0x00E1, 0x0141=>0x0142, 0x038E=>0x03CD, 0x0100=>0x0101,
	    0x0490=>0x0491, 0x0394=>0x03B4, 0x015A=>0x015B, 0x0044=>0x0064, 0x0393=>0x03B3,
	    0x00D4=>0x00F4, 0x042A=>0x044A, 0x0419=>0x0439, 0x0112=>0x0113, 0x041C=>0x043C,
	    0x015E=>0x015F, 0x0143=>0x0144, 0x00CE=>0x00EE, 0x040E=>0x045E, 0x042F=>0x044F,
	    0x039A=>0x03BA, 0x0154=>0x0155, 0x0049=>0x0069, 0x0053=>0x0073, 0x1E1E=>0x1E1F,
	    0x0134=>0x0135, 0x0427=>0x0447, 0x03A0=>0x03C0, 0x0418=>0x0438, 0x00D3=>0x00F3,
	    0x0420=>0x0440, 0x0404=>0x0454, 0x0415=>0x0435, 0x0429=>0x0449, 0x014A=>0x014B,
	    0x0411=>0x0431, 0x0409=>0x0459, 0x1E02=>0x1E03, 0x00D6=>0x00F6, 0x00D9=>0x00F9,
	    0x004E=>0x006E, 0x0401=>0x0451, 0x03A4=>0x03C4, 0x0423=>0x0443, 0x015C=>0x015D,
	    0x0403=>0x0453, 0x03A8=>0x03C8, 0x0158=>0x0159, 0x0047=>0x0067, 0x00C4=>0x00E4,
	    0x0386=>0x03AC, 0x0389=>0x03AE, 0x0166=>0x0167, 0x039E=>0x03BE, 0x0164=>0x0165,
	    0x0116=>0x0117, 0x0108=>0x0109, 0x0056=>0x0076, 0x00DE=>0x00FE, 0x0156=>0x0157,
	    0x00DA=>0x00FA, 0x1E60=>0x1E61, 0x1E82=>0x1E83, 0x00C2=>0x00E2, 0x0118=>0x0119,
	    0x0145=>0x0146, 0x0050=>0x0070, 0x0150=>0x0151, 0x042E=>0x044E, 0x0128=>0x0129,
	    0x03A7=>0x03C7, 0x013D=>0x013E, 0x0422=>0x0442, 0x005A=>0x007A, 0x0428=>0x0448,
	    0x03A1=>0x03C1, 0x1E80=>0x1E81, 0x016C=>0x016D, 0x00D5=>0x00F5, 0x0055=>0x0075,
	    0x0176=>0x0177, 0x00DC=>0x00FC, 0x1E56=>0x1E57, 0x03A3=>0x03C3, 0x041A=>0x043A,
	    0x004D=>0x006D, 0x016A=>0x016B, 0x0170=>0x0171, 0x0424=>0x0444, 0x00CC=>0x00EC,
	    0x0168=>0x0169, 0x039F=>0x03BF, 0x004B=>0x006B, 0x00D2=>0x00F2, 0x00C0=>0x00E0,
	    0x0414=>0x0434, 0x03A9=>0x03C9, 0x1E6A=>0x1E6B, 0x00C3=>0x00E3, 0x042D=>0x044D,
	    0x0416=>0x0436, 0x01A0=>0x01A1, 0x010C=>0x010D, 0x011C=>0x011D, 0x00D0=>0x00F0,
	    0x013B=>0x013C, 0x040F=>0x045F, 0x040A=>0x045A, 0x00C8=>0x00E8, 0x03A5=>0x03C5,
	    0x0046=>0x0066, 0x00DD=>0x00FD, 0x0043=>0x0063, 0x021A=>0x021B, 0x00CA=>0x00EA,
	    0x0399=>0x03B9, 0x0179=>0x017A, 0x00CF=>0x00EF, 0x01AF=>0x01B0, 0x0045=>0x0065,
	    0x039B=>0x03BB, 0x0398=>0x03B8, 0x039C=>0x03BC, 0x040C=>0x045C, 0x041F=>0x043F,
	    0x042C=>0x044C, 0x00DE=>0x00FE, 0x00D0=>0x00F0, 0x1EF2=>0x1EF3, 0x0048=>0x0068,
	    0x00CB=>0x00EB, 0x0110=>0x0111, 0x0413=>0x0433, 0x012E=>0x012F, 0x00C6=>0x00E6,
	    0x0058=>0x0078, 0x0160=>0x0161, 0x016E=>0x016F, 0x0391=>0x03B1, 0x0407=>0x0457,
	    0x0172=>0x0173, 0x0178=>0x00FF, 0x004F=>0x006F, 0x041B=>0x043B, 0x0395=>0x03B5,
	    0x0425=>0x0445, 0x0120=>0x0121, 0x017D=>0x017E, 0x017B=>0x017C, 0x0396=>0x03B6,
	    0x0392=>0x03B2, 0x0388=>0x03AD, 0x1E84=>0x1E85, 0x0174=>0x0175, 0x0051=>0x0071,
	    0x0417=>0x0437, 0x1E0A=>0x1E0B, 0x0147=>0x0148, 0x0104=>0x0105, 0x0408=>0x0458,
	    0x014C=>0x014D, 0x00CD=>0x00ED, 0x0059=>0x0079, 0x010A=>0x010B, 0x038F=>0x03CE,
	    0x0052=>0x0072, 0x0410=>0x0430, 0x0405=>0x0455, 0x0402=>0x0452, 0x0126=>0x0127,
	    0x0136=>0x0137, 0x012A=>0x012B, 0x038A=>0x03AF, 0x042B=>0x044B, 0x004C=>0x006C,
	    0x0397=>0x03B7, 0x0124=>0x0125, 0x0218=>0x0219, 0x00DB=>0x00FB, 0x011E=>0x011F,
	    0x041E=>0x043E, 0x1E40=>0x1E41, 0x039D=>0x03BD, 0x0106=>0x0107, 0x03AB=>0x03CB,
	    0x0426=>0x0446, 0x00DE=>0x00FE, 0x00C7=>0x00E7, 0x03AA=>0x03CA, 0x0421=>0x0441,
	    0x0412=>0x0432, 0x010E=>0x010F, 0x00D8=>0x00F8, 0x0057=>0x0077, 0x011A=>0x011B,
	    0x0054=>0x0074, 0x004A=>0x006A, 0x040B=>0x045B, 0x0406=>0x0456, 0x0102=>0x0103,
	    0x039B=>0x03BB, 0x00D1=>0x00F1, 0x041D=>0x043D, 0x038C=>0x03CC, 0x00C9=>0x00E9,
	    0x00D0=>0x00F0, 0x0407=>0x0457, 0x0122=>0x0123,
	            );
	    }
	    $uni = self::utf8_to_unicode($string);
	    if ( !$uni ) {
	        return FALSE;
	    }
	    $cnt = count($uni);
	    for ($i = 0; $i < $cnt; $i++){
	        if ( isset($UTF8_UPPER_TO_LOWER[$uni[$i]]) ) {
	            $uni[$i] = $UTF8_UPPER_TO_LOWER[$uni[$i]];
	        }
	    }
	    return self::utf8_from_unicode($uni);
	}
	public static function toUpper($string) {
		$string = (string)$string;
	    static $UTF8_LOWER_TO_UPPER = NULL;
	    if ( is_null($UTF8_LOWER_TO_UPPER) ) {
	        $UTF8_LOWER_TO_UPPER = array(
	    0x0061=>0x0041, 0x03C6=>0x03A6, 0x0163=>0x0162, 0x00E5=>0x00C5, 0x0062=>0x0042,
	    0x013A=>0x0139, 0x00E1=>0x00C1, 0x0142=>0x0141, 0x03CD=>0x038E, 0x0101=>0x0100,
	    0x0491=>0x0490, 0x03B4=>0x0394, 0x015B=>0x015A, 0x0064=>0x0044, 0x03B3=>0x0393,
	    0x00F4=>0x00D4, 0x044A=>0x042A, 0x0439=>0x0419, 0x0113=>0x0112, 0x043C=>0x041C,
	    0x015F=>0x015E, 0x0144=>0x0143, 0x00EE=>0x00CE, 0x045E=>0x040E, 0x044F=>0x042F,
	    0x03BA=>0x039A, 0x0155=>0x0154, 0x0069=>0x0049, 0x0073=>0x0053, 0x1E1F=>0x1E1E,
	    0x0135=>0x0134, 0x0447=>0x0427, 0x03C0=>0x03A0, 0x0438=>0x0418, 0x00F3=>0x00D3,
	    0x0440=>0x0420, 0x0454=>0x0404, 0x0435=>0x0415, 0x0449=>0x0429, 0x014B=>0x014A,
	    0x0431=>0x0411, 0x0459=>0x0409, 0x1E03=>0x1E02, 0x00F6=>0x00D6, 0x00F9=>0x00D9,
	    0x006E=>0x004E, 0x0451=>0x0401, 0x03C4=>0x03A4, 0x0443=>0x0423, 0x015D=>0x015C,
	    0x0453=>0x0403, 0x03C8=>0x03A8, 0x0159=>0x0158, 0x0067=>0x0047, 0x00E4=>0x00C4,
	    0x03AC=>0x0386, 0x03AE=>0x0389, 0x0167=>0x0166, 0x03BE=>0x039E, 0x0165=>0x0164,
	    0x0117=>0x0116, 0x0109=>0x0108, 0x0076=>0x0056, 0x00FE=>0x00DE, 0x0157=>0x0156,
	    0x00FA=>0x00DA, 0x1E61=>0x1E60, 0x1E83=>0x1E82, 0x00E2=>0x00C2, 0x0119=>0x0118,
	    0x0146=>0x0145, 0x0070=>0x0050, 0x0151=>0x0150, 0x044E=>0x042E, 0x0129=>0x0128,
	    0x03C7=>0x03A7, 0x013E=>0x013D, 0x0442=>0x0422, 0x007A=>0x005A, 0x0448=>0x0428,
	    0x03C1=>0x03A1, 0x1E81=>0x1E80, 0x016D=>0x016C, 0x00F5=>0x00D5, 0x0075=>0x0055,
	    0x0177=>0x0176, 0x00FC=>0x00DC, 0x1E57=>0x1E56, 0x03C3=>0x03A3, 0x043A=>0x041A,
	    0x006D=>0x004D, 0x016B=>0x016A, 0x0171=>0x0170, 0x0444=>0x0424, 0x00EC=>0x00CC,
	    0x0169=>0x0168, 0x03BF=>0x039F, 0x006B=>0x004B, 0x00F2=>0x00D2, 0x00E0=>0x00C0,
	    0x0434=>0x0414, 0x03C9=>0x03A9, 0x1E6B=>0x1E6A, 0x00E3=>0x00C3, 0x044D=>0x042D,
	    0x0436=>0x0416, 0x01A1=>0x01A0, 0x010D=>0x010C, 0x011D=>0x011C, 0x00F0=>0x00D0,
	    0x013C=>0x013B, 0x045F=>0x040F, 0x045A=>0x040A, 0x00E8=>0x00C8, 0x03C5=>0x03A5,
	    0x0066=>0x0046, 0x00FD=>0x00DD, 0x0063=>0x0043, 0x021B=>0x021A, 0x00EA=>0x00CA,
	    0x03B9=>0x0399, 0x017A=>0x0179, 0x00EF=>0x00CF, 0x01B0=>0x01AF, 0x0065=>0x0045,
	    0x03BB=>0x039B, 0x03B8=>0x0398, 0x03BC=>0x039C, 0x045C=>0x040C, 0x043F=>0x041F,
	    0x044C=>0x042C, 0x00FE=>0x00DE, 0x00F0=>0x00D0, 0x1EF3=>0x1EF2, 0x0068=>0x0048,
	    0x00EB=>0x00CB, 0x0111=>0x0110, 0x0433=>0x0413, 0x012F=>0x012E, 0x00E6=>0x00C6,
	    0x0078=>0x0058, 0x0161=>0x0160, 0x016F=>0x016E, 0x03B1=>0x0391, 0x0457=>0x0407,
	    0x0173=>0x0172, 0x00FF=>0x0178, 0x006F=>0x004F, 0x043B=>0x041B, 0x03B5=>0x0395,
	    0x0445=>0x0425, 0x0121=>0x0120, 0x017E=>0x017D, 0x017C=>0x017B, 0x03B6=>0x0396,
	    0x03B2=>0x0392, 0x03AD=>0x0388, 0x1E85=>0x1E84, 0x0175=>0x0174, 0x0071=>0x0051,
	    0x0437=>0x0417, 0x1E0B=>0x1E0A, 0x0148=>0x0147, 0x0105=>0x0104, 0x0458=>0x0408,
	    0x014D=>0x014C, 0x00ED=>0x00CD, 0x0079=>0x0059, 0x010B=>0x010A, 0x03CE=>0x038F,
	    0x0072=>0x0052, 0x0430=>0x0410, 0x0455=>0x0405, 0x0452=>0x0402, 0x0127=>0x0126,
	    0x0137=>0x0136, 0x012B=>0x012A, 0x03AF=>0x038A, 0x044B=>0x042B, 0x006C=>0x004C,
	    0x03B7=>0x0397, 0x0125=>0x0124, 0x0219=>0x0218, 0x00FB=>0x00DB, 0x011F=>0x011E,
	    0x043E=>0x041E, 0x1E41=>0x1E40, 0x03BD=>0x039D, 0x0107=>0x0106, 0x03CB=>0x03AB,
	    0x0446=>0x0426, 0x00FE=>0x00DE, 0x00E7=>0x00C7, 0x03CA=>0x03AA, 0x0441=>0x0421,
	    0x0432=>0x0412, 0x010F=>0x010E, 0x00F8=>0x00D8, 0x0077=>0x0057, 0x011B=>0x011A,
	    0x0074=>0x0054, 0x006A=>0x004A, 0x045B=>0x040B, 0x0456=>0x0406, 0x0103=>0x0102,
	    0x03BB=>0x039B, 0x00F1=>0x00D1, 0x043D=>0x041D, 0x03CC=>0x038C, 0x00E9=>0x00C9,
	    0x00F0=>0x00D0, 0x0457=>0x0407, 0x0123=>0x0122,
	            );
	    }
	    $uni = self::utf8_to_unicode($string);
	    if ( !$uni ) {
	        return FALSE;
	    }
	    $cnt = count($uni);
	    for ($i = 0; $i < $cnt; $i++){
	        if( isset($UTF8_LOWER_TO_UPPER[$uni[$i]]) ) {
	            $uni[$i] = $UTF8_LOWER_TO_UPPER[$uni[$i]];
	        }
	    }
	    return self::utf8_from_unicode($uni);
	}
	public static function str_split($string, $length = null) {
		$string = (string)$string;
	    if (!is_string($string)) trigger_error('A string type expected in first parameter, ' . gettype($string) . ' given!', E_USER_ERROR);
	    $length = ($length === null) ? 1 : intval($length);
	    if ($length < 1) return false;
	    #there are limits in regexp for {min,max}!
	    if ($length < 100)
	    {
	        preg_match_all('/(?>[\x09\x0A\x0D\x20-\x7E]           # ASCII
	                          | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
	                          |  \xE0[\xA0-\xBF][\x80-\xBF]       # excluding overlongs
	                          | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
	                          |  \xED[\x80-\x9F][\x80-\xBF]       # excluding surrogates
	                          |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
	                          | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
	                          |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
	                          #| (.)                               # catch bad bytes
	                         ){1,' . $length . '}
	                        /xsS', $string, $m);
	        $a =& $m[0];
	    }
	    else
	    {
	        preg_match_all('/(?>[\x09\x0A\x0D\x20-\x7E]           # ASCII
	                          | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
	                          |  \xE0[\xA0-\xBF][\x80-\xBF]       # excluding overlongs
	                          | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
	                          |  \xED[\x80-\x9F][\x80-\xBF]       # excluding surrogates
	                          |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
	                          | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
	                          |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
	                          #| (.)                               # catch bad bytes
	                         )
	                        /xsS', $string, $m);
	        $a = array();
	        for ($i = 0, $c = count($m[0]); $i < $c; $i += $length)
				$a[] = implode('', array_slice($m[0], $i, $length));
	    }
	    #check UTF-8 data
	    $distance = strlen($string) - strlen(implode('', $a));
	    if ($distance > 0)
	    {
	        trigger_error('Charset is not UTF-8, total ' . $distance . ' unknown bytes found!', E_USER_WARNING);
	        return false;
	    }
	    return $a;
	}
	public static function utf8_to_unicode($str) {
	    $mState = 0;     // cached expected number of octets after the current octet
	                     // until the beginning of the next UTF8 character sequence
	    $mUcs4  = 0;     // cached Unicode character
	    $mBytes = 1;     // cached expected number of octets in the current sequence

	    $out = array();

	    $len = strlen($str);

	    for($i = 0; $i < $len; $i++) {

	        $in = ord($str{$i});

	        if ( $mState == 0) {

	            // When mState is zero we expect either a US-ASCII character or a
	            // multi-octet sequence.
	            if (0 == (0x80 & ($in))) {
	                // US-ASCII, pass straight through.
	                $out[] = $in;
	                $mBytes = 1;

	            } else if (0xC0 == (0xE0 & ($in))) {
	                // First octet of 2 octet sequence
	                $mUcs4 = ($in);
	                $mUcs4 = ($mUcs4 & 0x1F) << 6;
	                $mState = 1;
	                $mBytes = 2;

	            } else if (0xE0 == (0xF0 & ($in))) {
	                // First octet of 3 octet sequence
	                $mUcs4 = ($in);
	                $mUcs4 = ($mUcs4 & 0x0F) << 12;
	                $mState = 2;
	                $mBytes = 3;

	            } else if (0xF0 == (0xF8 & ($in))) {
	                // First octet of 4 octet sequence
	                $mUcs4 = ($in);
	                $mUcs4 = ($mUcs4 & 0x07) << 18;
	                $mState = 3;
	                $mBytes = 4;

	            } else if (0xF8 == (0xFC & ($in))) {
	                /* First octet of 5 octet sequence.
	                *
	                * This is illegal because the encoded codepoint must be either
	                * (a) not the shortest form or
	                * (b) outside the Unicode range of 0-0x10FFFF.
	                * Rather than trying to resynchronize, we will carry on until the end
	                * of the sequence and let the later error handling code catch it.
	                */
	                $mUcs4 = ($in);
	                $mUcs4 = ($mUcs4 & 0x03) << 24;
	                $mState = 4;
	                $mBytes = 5;

	            } else if (0xFC == (0xFE & ($in))) {
	                // First octet of 6 octet sequence, see comments for 5 octet sequence.
	                $mUcs4 = ($in);
	                $mUcs4 = ($mUcs4 & 1) << 30;
	                $mState = 5;
	                $mBytes = 6;

	            } else {
	                /* Current octet is neither in the US-ASCII range nor a legal first
	                 * octet of a multi-octet sequence.
	                 */
	                trigger_error(
	                        'utf8_to_unicode: Illegal sequence identifier '.
	                            'in UTF-8 at byte '.$i,
	                        E_USER_WARNING
	                    );
	                return FALSE;

	            }

	        } else {

	            // When mState is non-zero, we expect a continuation of the multi-octet
	            // sequence
	            if (0x80 == (0xC0 & ($in))) {

	                // Legal continuation.
	                $shift = ($mState - 1) * 6;
	                $tmp = $in;
	                $tmp = ($tmp & 0x0000003F) << $shift;
	                $mUcs4 |= $tmp;

	                /**
	                * End of the multi-octet sequence. mUcs4 now contains the final
	                * Unicode codepoint to be output
	                */
	                if (0 == --$mState) {

	                    /*
	                    * Check for illegal sequences and codepoints.
	                    */
	                    // From Unicode 3.1, non-shortest form is illegal
	                    if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
	                        ((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
	                        ((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
	                        (4 < $mBytes) ||
	                        // From Unicode 3.2, surrogate characters are illegal
	                        (($mUcs4 & 0xFFFFF800) == 0xD800) ||
	                        // Codepoints outside the Unicode range are illegal
	                        ($mUcs4 > 0x10FFFF)) {

	                        trigger_error(
	                                'utf8_to_unicode: Illegal sequence or codepoint '.
	                                    'in UTF-8 at byte '.$i,
	                                E_USER_WARNING
	                            );

	                        return FALSE;

	                    }

	                    if (0xFEFF != $mUcs4) {
	                        // BOM is legal but we don't want to output it
	                        $out[] = $mUcs4;
	                    }

	                    //initialize UTF8 cache
	                    $mState = 0;
	                    $mUcs4  = 0;
	                    $mBytes = 1;
	                }

	            } else {
	                /**
	                *((0xC0 & (*in) != 0x80) && (mState != 0))
	                * Incomplete multi-octet sequence.
	                */
	                trigger_error(
	                        'utf8_to_unicode: Incomplete multi-octet '.
	                        '   sequence in UTF-8 at byte '.$i,
	                        E_USER_WARNING
	                    );

	                return FALSE;
	            }
	        }
	    }
	    return $out;
	}
	public static function utf8_from_unicode($arr) {
	    ob_start();

	    foreach (array_keys($arr) as $k) {

	        # ASCII range (including control chars)
	        if ( ($arr[$k] >= 0) && ($arr[$k] <= 0x007f) ) {

	            echo chr($arr[$k]);

	        # 2 byte sequence
	        } else if ($arr[$k] <= 0x07ff) {

	            echo chr(0xc0 | ($arr[$k] >> 6));
	            echo chr(0x80 | ($arr[$k] & 0x003f));

	        # Byte order mark (skip)
	        } else if($arr[$k] == 0xFEFF) {

	            // nop -- zap the BOM

	        # Test for illegal surrogates
	        } else if ($arr[$k] >= 0xD800 && $arr[$k] <= 0xDFFF) {

	            // found a surrogate
	            trigger_error(
	                'utf8_from_unicode: Illegal surrogate '.
	                    'at index: '.$k.', value: '.$arr[$k],
	                E_USER_WARNING
	                );

	            return FALSE;

	        # 3 byte sequence
	        } else if ($arr[$k] <= 0xffff) {

	            echo chr(0xe0 | ($arr[$k] >> 12));
	            echo chr(0x80 | (($arr[$k] >> 6) & 0x003f));
	            echo chr(0x80 | ($arr[$k] & 0x003f));

	        # 4 byte sequence
	        } else if ($arr[$k] <= 0x10ffff) {

	            echo chr(0xf0 | ($arr[$k] >> 18));
	            echo chr(0x80 | (($arr[$k] >> 12) & 0x3f));
	            echo chr(0x80 | (($arr[$k] >> 6) & 0x3f));
	            echo chr(0x80 | ($arr[$k] & 0x3f));

	        } else {

	            trigger_error(
	                'utf8_from_unicode: Codepoint out of Unicode range '.
	                    'at index: '.$k.', value: '.$arr[$k],
	                E_USER_WARNING
	                );

	            // out of range
	            return FALSE;
	        }
	    }

	    $result = ob_get_contents();
	    ob_end_clean();
	    return $result;
	}
}

function fstrtolower($str) {return STR::toLower($str);}
function fstrtoupper($str) {return STR::toUpper($str);}

function htmlsec($str) {
	$str = (string)$str;
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function unhtmlsec($str) {
	$str = (string)$str;
	$str = str_replace('&lt;', '<', $str);
	$str = str_replace('&gt;', '>', $str);
	$str = str_replace('&#039;', "'", $str);
	$str = str_replace('&quot;', '"', $str);
	$str = str_replace('&amp;', '&', $str);
	return $str;
}

function letAndNum($str) {
	$str = (string)$str;
	$str = preg_replace("/[^A-Za-zА-Яа-я0-9]+/uis", "", $str);
	return $str;
}

function getRndStr($length, $template = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
	mt_srand(makeSeed());
	$template_length = STR::length($template) - 1;
	for ($i = 0, $rndstring = ""; $i < $length; $i++) {
		$b = mt_rand(0, $template_length);
		$rndstring .= $template{$b};
	}
	return $rndstring;
}

function dateAndTime($text) {
	$text = (string)$text;
	$dt = explode(" ", $text);
	$d = explode("-", @$dt[0]);
	$t = explode(":", @$dt[1]);
	if (count($t) != 3) $t = array(12, 0, 0);
	if (count($d) != 3) return 0;
	if ($d[0] < 1900) return 0;
	return (int)mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]);
}

function onlyNumbers($str) {
	return preg_replace("#[^0-9]+#uis", "", (string)$str);
}

function spaces($str) {
	$str = (string)$str;
	$str = preg_replace("#(.)#uis", "$1<br />", $str);
	return $str;
}

function make_plural($n, $v1, $v2, $v3) {
	return $n % 100 < 10 || $n % 100 > 20 ? ($n % 10 == 1 ? $v1 : ($n % 10 >= 2 && $n % 10 <= 4 ? $v2 : $v3)) : $v3;
}

function make_texter($n, $f = false) {
	$a = array();
	$a[0] = array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять', 'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семьнадцать', 'восемьнадцать', 'девятнадцать');
	$a[1] = array('', 'десять', 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
	$a[2] = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
	$a[3] = array('', 'одна тысяча', 'две тысячи', 'три тысячи', 'четыре тысячи', 'пять тысяч', 'шесть тысяч', 'семь тысяч', 'восемь тысяч', 'девять тысяч', 'десять тысяч', 'одиннадцать тысяч', 'двенадцать тысяч', 'тринадцать тысяч', 'четырнадцать тысяч', 'пятнадцать тысяч', 'шестнадцать тысяч', 'семьнадцать тысяч', 'восемьнадцать тысяч', 'девятнадцать тысяч');
	$a[4] = array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять', 'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семьнадцать', 'восемьнадцать', 'девятнадцать');
	if ($n < 0) return 'меньше нуля';
	if ($n == 0) return 'ноль';
	if ($n >= 10000000) return 'очень много';
	$s = '';
	$flag1 = false;
	if ($n >= 1000000 && $n <= 9999999) {
		$m = floor($n / 1000000);
		$n = $n % 1000000;
		$s .= ' ' . $a[0][$m] . ' ' . make_plural($m, 'миллион', 'миллиона', 'миллионов');
	}
	if ($n >= 100000 && $n <= 999999) {
		$m = floor($n / 100000);
		$n = $n % 100000;
		$s .= ' ' . $a[2][$m];
		$flag1 = true;
	}
	if ($n >= 20000 && $n <= 99999) {
		$m = floor($n / 1000);
		$n = $n % 1000;
		$p1 = floor($m / 10);
		$p2 = $m % 10;
		$s .= ' ' . $a[1][$p1] . ' ' . $a[4][$p2] . ' ' . make_plural($m, 'тысяча', 'тысячи', 'тысяч');
	} elseif ($n >= 1000 && $n <= 19999) {
		$m = floor($n / 1000);
		$n = $n % 1000;
		$s .= ' ' . $a[3][$m];
	} elseif ($flag1) {
		$s .= ' тысяч';
	}
	if ($n >= 100 && $n <= 999) {
		$m = floor($n / 100);
		$n = $n % 100;
		$s .= ' ' . $a[2][$m];
	}
	if ($n >= 20 && $n <= 99) {
		$m = floor($n / 10);
		$n = $n % 10;
		$s .= ' ' . $a[1][$m] . ' ' . $a[0][$n];
	} elseif ($n >= 1 && $n <= 19) {
		$s .= ' ' . $a[0][$n];
	}
	$s = trim(preg_replace('# +#uis', ' ', $s));
	switch ($f) {
		case 'up':
			$s = STR::toUpper($s);
		break;
		case 'uc':
			$s = STR::ucfirst($s);
		break;
	}
	return $s;
}

function make_plus($n) {
	return $n > 0 ? '+' . $n : $n;
}

function pctshow($n, $f = null) {
	return $n == 100 ? '100' : (round($n, 1) > 0 ? sprintf('%4.1f', $n) : ($f !== null ? $f : '0'));
}

function numshow($n, $p = 0, $s = '', $z = '') {
	$p = abs((int)$p);
	return round($n, $p) > 0 ? sprintf('%.' . $p . 'f', round($n, $p)) . $s : $z;
}

function writeDuration($val, $flag = 0) {
	$h = 0;
	if ($val > 3600) {
		$h = floor($val / 3600);
		$val = $val % 3600;
	}
	$m = floor($val / 60);
	$s = $val % 60;
	if ($flag == 2) {
		return sprintf('%02d', $h);
	} elseif ($flag == 1) {
		return sprintf('%02d:%02d', $h, $m);
	} else {
		return $h ? sprintf('%02d:%02d:%02d', $h, $m, $s) : sprintf('%02d:%02d', $m, $s);
	}
}

function is_nnum($a) {
	return ((string)$a == abs((int)$a));
}

function is_letter($a) {
	return (bool)preg_match("#^[a-zA-Z]$#uis", $a);
}

function array_iunique($array) {
	return array_intersect_key(
		$array,
		array_unique(array_map(function($a){
			return mb_strtolower($a, 'UTF-8');
		}, $array))
	);
}

function priceshow($n) {
	$n = abs((int)$n);
	if ($n < 1000) return (string)$n;
	if ($n < 1000000) return preg_replace("#^([0-9]{1,3})([0-9]{3})$#uis", "$1&nbsp;$2", $n);
	if ($n < 1000000000) return preg_replace("#^([0-9]{1,3})([0-9]{3})([0-9]{3})$#uis", "$1&nbsp;$2&nbsp;$3", $n);
	return preg_replace("#^([0-9]+)([0-9]{3})([0-9]{3})([0-9]{3})$#uis", "$1&nbsp;$2&nbsp;$3&nbsp;$4", $n);
}
