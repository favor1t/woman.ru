<?php

/**
 * Хелпер для работы со списками стоп-слов
 */ 
 
class StopWordHelper
{

  /*

  цифарки для таблицы woman_stop_word_check_fail:

  target:
  1 = forum topic
  2 = forum message
  3 = consultation
  4 = comment

  field:
  1 = user name
  2 = body

  site:
  1 = www
  2 = mobile

  */


  const CACHE_TAG = 'stoplist';

  private const LOG_TYPE_SPAM  = 1;
  private const LOG_TYPE_ABUSE = 2;


  private $stopWords;
  private $abuseWords;

  private static $arrPatternStopWord = null;


  /**
   * какая-то непонятная одиночка =\
   * @return StopWordHelper
   */
  public static function getInstance() : self
  {
    static $instance = null;

    if(! $instance) $instance = new self();
    return $instance;
  }


    private static function normalizeText(string $text) : string
    {
      $text = preg_replace("/[^a-z\dа-яёЁ\s]/iu", " ", mb_strtolower($text));
      $text = preg_replace("/\s+/iu", " ", $text);
      $text = preg_replace("/(\d)\s(\d)/iu", "$1$2", $text);

      return trim($text);
    }


    /**
     * нормализация: выносим все, кроме буковок
     * @param string $text
     * @return string
     */
    private static function normalizeTextNew(string $text) : string
    {
      return mb_strtolower(trim(preg_replace('#[^а-яa-z0-9 ]#iuU', '', $text)));
    }


    /**
     * на вход дается стоп-слово, на выходе - регулярка
     * @param string $stopWord
     * @return string
     */
    private static function createRegexpByStopWord(string $stopWord) : string
    {
      $array =
      [
        'а' => [ 'а', 'a', '@',   ],
        'б' => [ 'б', '6', 'b',   ],
        'в' => [ 'в', 'b', 'v',   ],
        'г' => [ 'г', 'r', 'g',   ],
        'д' => [ 'д', 'd', 'g',   ],
        'е' => [ 'е', 'e',        ],
        'ё' => [ 'ё', 'е', 'e',   ],
        'ж' => [ 'ж', 'zh', '\*', ],
        'з' => [ 'з', '3', 'z',   ],
        'и' => [ 'и', 'u', 'i',   ],
        'й' => [ 'й', 'u', 'y', 'i',   ],
        'к' => [ 'к', 'k', 'i{', '|{', ],
        'л' => [ 'л', 'l', 'ji', ],
        'м' => [ 'м', 'm',       ],
        'н' => [ 'н', 'h', 'n',  ],
        'о' => [ 'о', 'o', '0',  ],
        'п' => [ 'п', 'n', 'p',  ],
        'р' => [ 'р', 'r', 'p',  ],
        'с' => [ 'с', 'c', 's',  ],
        'т' => [ 'т', 'm', 't',  ],
        'у' => [ 'у', 'y', 'u',  ],
        'ф' => [ 'ф', 'f',       ],
        'х' => [ 'х', 'x', 'h', 'к', 'k', '}{', ],
        'ц' => [ 'ц', 'c', 'u,', ],
        'ч' => [ 'ч', 'ch',      ],
        'ш' => [ 'ш', 'sh',      ],
        'щ' => [ 'щ', 'sch',     ],
        'ь' => [ 'ь', 'b',       ],
        'ы' => [ 'ы', 'bi',      ],
        'ъ' => [ 'ъ',            ],
        'э' => [ 'э', 'е', 'e',  ],
        'ю' => [ 'ю', 'io',      ],
        'я' => [ 'я', 'ya',      ],
      ];

      $arrChar = TextHelper::str_split_unicode($stopWord, $size = 1);
      foreach($arrChar as $index => $char)
      {
        if(! isset($array[$char])) continue;
        $arrChar[$index] = '('.implode('|', $array[$char]).')';
      }
      return implode('', $arrChar);
    }


  /**
   * возвращает булевое: в строке НЕТ запрещенных слов?
   * именно стоп-слов, а НЕ матерных
   * @param $text
   * @param array $arrParamLog
   * @return bool
   */
    public static function checkStopWords($text, array $arrParamLog = [])
    {
      $textCheck = self::normalizeTextNew($text);
      if($textCheck == '') return true;

      foreach(self::getRegexpStopWord() as $regexp)
      {
        if(! preg_match($regexp, $textCheck, $arrMatch)) continue;
        self::logCheckStopWordsFail($text, $textCheck, $arrMatch[0], $arrParamLog);
        return false;
      }

	    return true;
	  }


    /**
     * кеширующая штука для преобразования списка стоп-слов в массив регулярок
     * @return string[]
     */
	  private static function getRegexpStopWord() : array
    {
      static $arrRegexp = null;
      if($arrRegexp) return $arrRegexp;

      $cache     = Yii::app()->cache;
      $cacheKey  = static::class.'~'.'stop_word';
      $arrRegexp = $cache->get($cacheKey);
      if($arrRegexp === false)
      {
        $arrRegexp  = self::realGetRegexpStopWord();
        $expire     = 60 * 60 * 12;
        $dependency = new TagsCacheDependency([ self::CACHE_TAG, ], $expire, $protectFromRaceCondition = true);
        $cache->set($cacheKey, $arrRegexp, $expire + 20, $dependency);
      }

      return $arrRegexp;
    }
    /**
     * штука для преобразования списка стоп-слов в массив регулярок
     * @return string[]
     */
    private static function realGetRegexpStopWord() : array
    {
      $arrRegexp = [];
      foreach(self::getInstance()->getActiveStopWords() as $wordCheck)
      {
        $wordCheck = self::normalizeTextNew($wordCheck);
        if($wordCheck == '') continue;

        $arrRegexp[] = self::createRegexpByStopWord($wordCheck);
      }

      $arrRegexp = array_chunk($arrRegexp, $size = 256);
      $arrRegexp = array_map(function(array $array) : string
      {
        return '/('.implode(')|(', $array).')/iU';
      }, $arrRegexp);

      return $arrRegexp;
    }


  /**
   * получает список запрещенных слов, возвращает массив строк-регулярок
   * @return string[]
   */
  private static function getAbuseReplacePattern() : array
  {
    if(self::$arrPatternStopWord === null)
    {
      self::$arrPatternStopWord = self::realGetAbuseReplacePattern();
      assert(self::$arrPatternStopWord !== null);
    }
    return self::$arrPatternStopWord;
  }
  /**
   * получает список запрещенных слов, возвращает массив строк-регулярок
   * @return string[]
   */
  private static function realGetAbuseReplacePattern() : array
  {
    $arrWord = StopWordHelper::getInstance()->getActiveAbuseWords();
    $arrWord = array_chunk($arrWord, $size = 1000);

    return array_map(function(array $arrWord) : string
    {
      $arrWord = array_map('trim', $arrWord);
      $arrWord = array_map('preg_quote', $arrWord);
      $pattern = implode('|', $arrWord);
      return '!(^|[^\w])('.$pattern.')($|[^\w])!uis';
    }, $arrWord);
  }


	public static function replaceAbuseWords($text)
  {
    $arrPattern = self::getAbuseReplacePattern();
    foreach($arrPattern as $pattern)
    {
      $text = preg_replace($pattern, '$1***$3', $text);
    }
    return $text;
	}



  public static function replaceAbuseWordsNew(string $text) : string
  {
    $instance = mt_rand();

    $arrRegexp = array_map(function(string $word) : string
    {
      $word = self::normalizeTextNew($word);
      return '#(^|[^\w])'.preg_quote($word).'($|[^\w])#iuU';
    }, StopWordHelper::getInstance()->getActiveAbuseWords());

    $arrWordCheck = explode(' ', $text);
    foreach($arrWordCheck as $index => $wordOriginal)
    {
      $wordCheck = self::normalizeTextNew($wordOriginal);
      foreach($arrRegexp as $regexp)
      {
        if(! preg_match($regexp, $wordCheck)) continue;
        //$arrWordCheck[$index] = '***';
        self::logAbuseWordFail($wordOriginal, $wordCheck, $regexp, $instance);
        break;
      }
    }
    return implode(' ', $arrWordCheck);
  }


  private static function logCheckStopWordsFail(string $textOriginal, string $textCheck, string $wordFail, array $arrParamLog = [])
  {
    self::log($type = self::LOG_TYPE_SPAM, $textOriginal, $textCheck, $wordFail, $instance = null, $arrParamLog);
  }
  private static function logAbuseWordFail(string $wordOriginal, string $wordCheck, string $regexp, int $instance)
  {
    self::log($type = self::LOG_TYPE_ABUSE, $wordOriginal, $wordCheck, $regexp, $instance);
  }
  private static function log(int $type, string $textOriginal, string $textCheck, string $wordFail, $instance = null, array $arrParamLog = [])
  {
    Db::execute('
      insert into {{stop_word_check_fail}} (created_at, text_original, text_check, word_fail, type, instance, target, field, site, user_agent, is_kaptcha_checked)
      values (now(), :text_original, :text_check, :word_fail, :type, :instance, :target, :field, :site, :user_agent, :is_kaptcha_checked)',
      [
        ':text_original'      => $textOriginal,
        ':text_check'         => $textCheck,
        ':word_fail'          => $wordFail,
        ':type'               => $type,
        ':instance'           => $instance,
        ':target'             => $arrParamLog['target'] ?? null,
        ':field'              => $arrParamLog['field'] ?? null,
        ':site'               => $arrParamLog['site'] ?? null,
        ':user_agent'         => $arrParamLog['user_agent'] ?? null,
        ':is_kaptcha_checked' => $arrParamLog['is_kaptcha_checked'] ?? null,
      ]);
  }

	
	
	public static function highlightAbuseWords($text, $pre = '<span class="stop-word">', $post = '</span>')
  {
    $arrPattern = self::getAbuseReplacePattern();
    foreach($arrPattern as $pattern)
    {
      // return  preg_replace($pattern, '$1' . $pre . '$2' . $post . '$3', $text);
      $text = preg_replace($pattern, '$1'.$pre.'$2'.$post.'$3', $text);
    }
    return $text;
	}


	
	public function getActiveStopWords(){
	    
	    if(!is_null($this->stopWords)) return $this->stopWords;
	    
	    $cache = \Yii::app()->cache;
	    	    
	    $key = $this->getCacheKeyForType('stop-words');
	    
        if($words = $cache->get($key)){
            $this->stopWords = $words;
            return $this->stopWords;
        }
        
        $words = self::getActiveStopWordsFromDb();
        
        $expires = 60 * 60 * 12;
        
        $dep = new TagsCacheDependency([self::CACHE_TAG], $expires, true);
			
        $cache->set($key, $words, $expires + 20, $dep);
        
        $this->stopWords = $words;
        
        return $words;
	}
	
	
	public function getActiveAbuseWords(){
	    
	    if(!is_null($this->abuseWords)) return $this->abuseWords;
	    
	    $cache = \Yii::app()->cache;
	    	    
	    $key = $this->getCacheKeyForType('abuse-words');
	    
        if($words = $cache->get($key)){
            $this->abuseWords = $words;
            return $this->abuseWords;
        }
        
        $words = self::getActiveAbuseWordsFromDb();
        
        $expires = 60 * 60 * 12;
        
        $dep = new TagsCacheDependency([self::CACHE_TAG], $expires, true);
			
        $cache->set($key, $words, $expires + 20, $dep);
        
        $this->abuseWords = $words;
        
        return $words;
    }

	
	private function getActiveStopWordsFromDb()
  {
	  return $this->getWordsFromDb(StopWord::TYPE_SPAM, true);
	}

	
	private function getActiveAbuseWordsFromDb()
  {
	  return $this->getWordsFromDb(StopWord::TYPE_ABUSE, true);
	}

	
	private function getWordsFromDb(int $type = -1, bool $onlyActive = true)
  {
//	    if(!is_numeric($type)) throw new \CException("Incorrect word type");
//	    if(!is_bool($onlyActive)) throw new \CException("Incorrect onlyActive flag value");
	    
    $criteria = new \CDbCriteria();
    $criteria->select = 'word';
    
    if($type != -1) $criteria->compare('t.word_type', $type);
    if($onlyActive) $criteria->compare("active", 1);
    
    return \Yii::app()->db->getCommandBuilder()->createFindCommand('{{stop_words}}', $criteria)->queryColumn();
	}
	
	
	public static function refreshCacheTags()
  {
    CacheTag::refresh(self::CACHE_TAG);
  }
	

	private static function getCacheKeyForType(string $type) : string
  {
	    return 'stoplist.'.$type;   
	}
    
    
  public static function canFilterAbuseWords() : bool
  {
    $app = \Yii::app();
    if(! isset($app->controller)) return false;
    if(! isset($app->controller->module)) return true;
    if(isset($app->controller->module->id) && $app->controller->module->id == 'mobile')    return true;
    if(isset($app->controller->module->id) && $app->controller->module->id == 'moderator') return true;

    return false;
  }

};

