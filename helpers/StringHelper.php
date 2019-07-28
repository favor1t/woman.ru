<?php
/**
 * String functions
 * Static methods only.
 */
class StringHelper
{
    /**
     * Обрезает строку по пробелу
     *
     * @param string $str Текст для обрезания
     * @param int $count Количество оставляемых слов
     * @return string
     */
    public static function cropByWord($str, $count, $lastSigns = "")
    {
        $result = self::crop($str, 0, "", $count);
        if($lastSigns != '' && strlen($str) !== strlen($result))
            $result .= $lastSigns;
        return $result;
    }

	/**
	 * Обрезает строку до определенной длины, до границы слова
	 * @param string $text
	 * @param string $length
	 * @return string
	 */
	public static function crop($text, $length, $appendText = '', $keepTags = '')
	{
		$text   = trim(strip_tags($text, $keepTags));
		$strLen = mb_strlen($text, 'UTF-8');
		if($length >= $strLen)
		{
			return $text;
		}

        $pos = mb_strrpos($text, ' ', $length - $strLen, 'UTF-8');
        $result = mb_substr($text, 0, ($pos ? $pos :$length), 'UTF-8');
		return mb_strlen($result, 'UTF-8') < $strLen ? $result.$appendText : $result;
	}

	/**
	 * Возвращает произвольную строку
	 * @param unknown_type $length
	 * @return string
	 */
	public static function random($length = 5)
	{
		return substr(str_shuffle(md5(uniqid())), 0, $length);
	}

	/**
	 * Возвращает уникальный hash
	 * @return string
	 */
	public static function hash()
	{
		return md5(rand(1000, 10000) . time());
	}

	/**
	 * Склоненяет строку в зависимости от числительного
	 * @param int $num
	 * @param string $string
	 * @return string
	 */
	public static function variation($num, $string)
	{
		if (preg_match('/[A-z]/', $string))
			return $num == 1 ? $string : $string.'s';

		$strings = array('альбом'       => array('раз', 'раза', 'раз'),
			'пользователь' => array('пользователь', 'пользователя', 'пользователей'),
			'день'         => array('день', 'дня', 'дней'),
			'гость'        => array('гость', 'гостя', 'гостей'),
			'год'          => array('год', 'года', 'лет'),
			'неделя'       => array('неделя', 'недели', 'недель'),
			'месяц'          => array('месяц', 'месяца', 'месяцев'),
			'день'          => array('день', 'дня', 'дней'),
			'час'          => array('час', 'часа', 'часов'),
			'минута'          => array('минута', 'минуты', 'минут'),
			'секунда'          => array('секунда', 'секунды', 'секунд'),
			'сообщение'          => array('сообщение', 'сообщения', 'сообщений'),
		);

		if (!isset($strings[$string])) return $string;

		$penult = mb_strlen($num) > 1 ? (int)mb_substr($num, -2, 1) : 0;
		$last   = (int)mb_substr($num, -1);

		if ($last == 1 && $penult != 1) return $strings[$string][0];
		else if ($last >= 2 && $last <= 4 && $penult != 1) return $strings[$string][1];
		else return $strings[$string][2];
	}

	/**
	 * Выбор формы слова в зависимости от количественного числительного
	 * @param int $n
	 * @param array $forms
	 * @return string
	 */
	public static function declination($n, $forms)
	{
		if ($n % 10 == 1 && $n % 100 != 11)
		{
			return $forms[0];
		}
		elseif ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20))
		{
			return $forms[1];
		}
		else
		{
			return $forms[2];
		}
	}

	public static function isInsideTag($position, $html)
	{
		return mb_strpos($html, '>', $position, 'utf-8') < mb_strpos($html, '<', $position, 'utf-8');
	}

	// внутри слова
	public static function isInsideWord($position, $html)
	{
		if ($position == 0 || $position == mb_strlen($html, 'utf-8'))
			return false;

		return mb_substr($html, $position - 1, 1, 'utf-8') !== ' ' && mb_substr($html, $position, 1, 'utf-8') !== ' ';
	}

	public static function ucfirst($str, $enc = 'utf-8')
	{
		return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc).mb_substr($str, 1, mb_strlen($str, $enc), $enc);
	}

	public static function chrToUtf8($code) {
		$code = (int) $code;
		if ($code < 0) return false;
		elseif ($code < 128) return chr($code);
		elseif ($code < 160) // Remove Windows Illegals Cars
		{
			if ($code==128) $code=8364;
			elseif ($code==129) $code=160; // not affected
			elseif ($code==130) $code=8218;
			elseif ($code==131) $code=402;
			elseif ($code==132) $code=8222;
			elseif ($code==133) $code=8230;
			elseif ($code==134) $code=8224;
			elseif ($code==135) $code=8225;
			elseif ($code==136) $code=710;
			elseif ($code==137) $code=8240;
			elseif ($code==138) $code=352;
			elseif ($code==139) $code=8249;
			elseif ($code==140) $code=338;
			elseif ($code==141) $code=160; // not affected
			elseif ($code==142) $code=381;
			elseif ($code==143) $code=160; // not affected
			elseif ($code==144) $code=160; // not affected
			elseif ($code==145) $code=8216;
			elseif ($code==146) $code=8217;
			elseif ($code==147) $code=8220;
			elseif ($code==148) $code=8221;
			elseif ($code==149) $code=8226;
			elseif ($code==150) $code=8211;
			elseif ($code==151) $code=8212;
			elseif ($code==152) $code=732;
			elseif ($code==153) $code=8482;
			elseif ($code==154) $code=353;
			elseif ($code==155) $code=8250;
			elseif ($code==156) $code=339;
			elseif ($code==157) $code=160; // not affected
			elseif ($code==158) $code=382;
			elseif ($code==159) $code=376;
		}
		if ($code < 2048) return chr(192 | ($code >> 6)) . chr(128 | ($code & 63));
		elseif ($code < 65536) return chr(224 | ($code >> 12)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
		else return chr(240 | ($code >> 18)) . chr(128 | (($code >> 12) & 63)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
	}

	public static function htmlEntityReplace($matches)
	{
		if ($matches[2])
		{
			return self::chrToUtf8(hexdec($matches[3]));
		} elseif ($matches[1])
		{
			return self::chrToUtf8($matches[3]);
		}
		switch ($matches[3])
		{
			case "nbsp": return self::chrToUtf8(160);
			case "iexcl": return self::chrToUtf8(161);
			case "cent": return self::chrToUtf8(162);
			case "pound": return self::chrToUtf8(163);
			case "curren": return self::chrToUtf8(164);
			case "yen": return self::chrToUtf8(165);
			case "quote": return self::chrToUtf8(34);
			//... etc with all named HTML entities
		}
		return false;
	}

	public static function htmlEntitiesToUtf8 ($string) // because of the html_entity_decode() bug with UTF-8
	{
		$string = preg_replace_callback('~&(#(x?))?([^;]+);~', function ($matches){
			if ($matches[2]){
				return self::chrToUtf8(hexdec($matches[3]));
			} elseif ($matches[1]){
				return self::chrToUtf8($matches[3]);
			}
			switch ($matches[3]){
				case "nbsp": return self::chrToUtf8(160);
				case "iexcl": return self::chrToUtf8(161);
				case "cent": return self::chrToUtf8(162);
				case "pound": return self::chrToUtf8(163);
				case "curren": return self::chrToUtf8(164);
				case "yen": return self::chrToUtf8(165);
				case "quote": return self::chrToUtf8(34);
				//... etc with all named HTML entities
			}
			/**
			 * no changes
			 */
			return $matches[0];
		}, $string);

		return $string;
	}

	public static function utf8ReplaceNonUtf8Symbols($text){
		$regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;
		return preg_replace($regex, '$1', $text);
	}

    private static function utf8_str_split($str) {
        // place each character of the string into and array
        $split=1;
        $array = array();
        for ( $i=0; $i < strlen( $str ); ){
            $value = ord($str[$i]);
            if($value > 127){
                if($value >= 192 && $value <= 223)
                    $split=2;
                elseif($value >= 224 && $value <= 239)
                    $split=3;
                elseif($value >= 240 && $value <= 247)
                    $split=4;
            }else{
                $split=1;
            }
            $key = NULL;
            for ( $j = 0; $j < $split; $j++, $i++ ) {
                $key .= $str[$i];
            }
            array_push($array, $key);
        }
        return $array;
    }

    public static function clearText($str)
    {
        $sru = 'ёйцукенгшщзхъфывапролджэячсмитьбю';
        $s1 = array_merge(
            self::utf8_str_split($sru),
            self::utf8_str_split(strtoupper($sru)),
            range('A', 'Z'),
            range('a','z'),
            range('0', '9'),
            array('@', '&',' ','#',';','%','?',':','(',')','-','_','=','+','[',']',',','.','/','\\')
        );
        $codes = array();
        for ($i=0; $i<count($s1); $i++){
            $codes[ord($s1[$i])] = true;
        }
        $str_s = self::utf8_str_split($str);
        for ($i=0; $i<count($str_s); $i++){
            if (!isset($codes[ord($str_s[$i])]))
                $str = str_replace($str_s[$i], ' ', $str);
        }
        return $str;
    }
   
    /**
     * Removes skip words (prepositions, pronouncs e.t.c.) from a string
     *
     * @param str Target string
     * @return String without skip words
     */ 
    public static function removeSkipWords($str) : string
    {
		$skipwords = 'в во и не на я меня мне мной мною быть есть буду будешь будет ' .
	                 'будем будут был было были была будь будьте что чего чему чем ' .
	                 'чём он его него ему нему им ним нем нём оно она ее её нее неё ' .
	                 'ей ней ею нею они их них ими ними с а как этот этого этому ' .
	                 'этим этом это эта этой эту эти этих этими вы вас вам вами ты ' .
	                 'тебя тебе тобой тобою к ко мы нас нам нами но по весь всего ' .
	                 'всему всем все всё вся всей всю всею всех всеми за тот того ' .
	                 'тому то та той ту те тех теми тем том у из свой своего своему ' .
                     'своим своем своём свое своё своя своей свою своею свои своих ' .
	                 'своими так о об же который которого которому которым котором ' .
	                 'которое которая которой которую которою которые которых которыми ' .
	                 'бы от мочь могу можешь может можем можете могут мог могла могло ' .
	                 'могли моги могите один одного одному одним одном одно одна одной ' .
                     'одну одною одни одних одними для такой такого такому таким таком ' .
	                 'такое такая такую такою такие таких такими вот только еще ещё ' .
	                 'наш нашего нашему нашем наше наша нашей нашу нашею наши наших ' .
	                 'нашими нашим да сам себя себе собой собою нет до при уже или ' .
	                 'если мой моего моему моим моем моём мое моё моя моей мою моею ' .
	                 'мои моих моими чтобы кто кого кому кем ком когда';
	    $skipwords = explode(' ', $skipwords);
	    return preg_replace('/\b(' . implode('|', $skipwords) . ')\b/', '', $str);
    }

    /**
     * Returns words count from HTML
     * @param $html Target text for counting
     * @return Words count
     */
    public static function countWordsByHtml(string $html) : int {
    	if (empty($html)) {
    		return 0;
    	}

        $html = \StringHelper::clearPlainText($html);
        $html = preg_replace('#<style.+</style>#isU', '', $html);
        $html = preg_replace('#<!--.+-->#iU', '', $html);
        $html = \StringHelper::removeSkipWords($html);

        $plainText = $html;
        $plainText = str_replace(["\r","\n"], ' ', $plainText);
        $plainText = preg_replace('/  +/', ' ', $plainText);

        return count(explode(' ', $plainText));
    }

     /**
     * Удаляет лишние переносы строк.
     * Удаляет переносы в начале и в конце.
     * Удаляет все задвоенные переносы, оставляет только один.
     *
     * @param string $string
     * @return string
     */
    public static function clearBreaks($string){
    	//return $string;
    	$string = preg_replace('/^(\s*<br[^>]*>\s*)+|(\s*<br[^>]*>\s*)+$/usi', '', $string);
    	$string = preg_replace('/&lt;br[^(&gt;)]*&gt;/musi', '<br>', $string);
    	$string = preg_replace('/(\s*<br[^>]*>\s*)+/musi', "<br />\n", $string);
    	
    	return $string;
    }
    
    /**
     * Удаляет тэги [quote]
     * Полезно, когда в сообщении содержатся поломатые теги, удалить их перед выводом на страницу.
     *
     * @param string $string
     * @return string
     */
    public static function removeQuoteTags($str){
    	return preg_replace('/\[\/?quote[^\]]*\]/uUis', '', $str);	
    }

    //чистим в рассылках переводы строк, всякие пробелы, табы
    public static function clearLetterBody($html, $replace = '')
    {
        $html = preg_replace('/ {2,}/',' ',$html);
        $html = str_replace(array("\r\n", "\r", "\n", "\t"), $replace, $html);

        return $html;
    }


    //чистим и склеиваем параграфы переносами
    public  static function clearPlainText($html)
    {
        if (empty($html))
            return '';

        //убрали левые теги
        $html = str_replace(['&lt;', '&gt;'], ['<', '>'], $html);
        $html = strip_tags($html, '<p>'); //удаляем все теги
        $html = str_replace('&nbsp;', ' ', $html);
        //разбираем параграфы заменяем их на пробел
        $html = preg_replace('#<p.*?>(.*?)<\/p>#is', ' ', $html);
        $html = preg_replace('/\s\s+/', ' ', $html);

        return $html;
    }

    //заменяем кавычки на «ёлочки»
    public static function replaceQuotes($html)
    {
        return preg_replace('/"([^"]+)"/', '«\1»', $html);
    }


    /**
     * Удаляет тэги [quote]
     * Полезно, когда в сообщении содержатся поломатые теги, удалить их перед выводом на страницу.
     *
     * @param string $html - текст
     * @param string $tags - список тегов, которые оставляем
     * @param boolean $br - обрабатываем лишние переносы
     * @param boolean $paragraph - преобразование парагрофов
     * @return string
     */
    public static function clearHtml($html, $tags='', $br = false, $paragraph = false)
    {
        if (empty($html))
            return '';
        
        $html = preg_replace("/<p>\s*<\/p>/i", '', $html);
        
        $html = str_replace(['&lt;', '&gt;'], ['<', '>'], $html);
        $html = strip_tags($html, $tags);
        $html = str_replace('&nbsp;', ' ', $html);

        if ($paragraph) {
            //разбираем параграфы
            $removeRegs = array(
                '#<p(.*?)>(.*?)</p>#is' => '$2<br/>',
                '!<br.?/?>!sui' => ' ',
            );
            foreach ($removeRegs as $source => $dest) {
                $html = preg_replace($source, $dest, $html);
            }
        }

        if ($br)
        {
            $html = self::clearBreaks($html);
        }

        return $html;
    }

    /**
     * Возвращает последнее слово в строке
     *
     * @param string $str Исходная строка
     * @param string &$left Левая часть строки, после нахождения последнего слова
     * @return string Последнее слово в строке
     */
    public static function lastWord(string $str, &$left) : string
    {
    	$str = trim($str);
    	$left = '';
    	$right = '';
    	if (strlen($str) == 0) {
			return '';
    	}
    	$last_word_start = strrpos($str, ' ');
    	if ($last_word_start !== false) {
    		$left = substr($str, 0, $last_word_start + 1);
    		$right = substr($str, $last_word_start + 1);
    	}
    	else {
    		$right = $str;
    	}
    	return $right;
    }

};
