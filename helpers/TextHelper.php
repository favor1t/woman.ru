<?php

class TextHelper
{

	// форматирует время в секундах
	public static function formatTimestampDiff($timestampDiff)
	{
		$hour   = floor($timestampDiff / 60 / 60);
		$minute = floor($timestampDiff / 60) % 60;
		$second = $timestampDiff % 60;
		if($hour)   return sprintf('%u:%02u:%02u', $hour, $minute, $second);
		if($minute) return sprintf('%u:%02u', $minute, $second);
		return $second.' sec';
	}



  /**
   * склонятель
   * TextHelper::declension($productCount, [ 'товар', 'товара', 'товаров' ]);
   * @param integer $count
   * @param array $arrWord
   * @return string
   */
  public static function declension($count, array $arrWord)
	{
		$count = $count % 100;
	  if($count >= 5 && $count <= 20) return $arrWord[2];

		$count = $count % 10;
		if($count == 1) return $arrWord[0];
		if($count >= 2 && $count <= 4) return $arrWord[1];

		return $arrWord[2];
	}


  /**
   * переводит первый символ строки в верхний регистр
   * @param $string
   * @return string
   */
  public static function ucfirst(string $string) : string
  {
    if($string == '') return $string;

    return mb_strtoupper(mb_substr($string, $offset = 0, $limit = 1)).mb_substr($string, $offset = 1);
  }
		

  public static function brToP(string $string) : string
  {
    $string = preg_replace('#<br>#iU', '</p><p>', $string);
    return '<p>'.$string.'</p>';
  }


  public static function doCamelCase($string)
  {
    return preg_replace_callback('/_\S/iU', function(array $arrMatch)
    {
      return strtoupper($arrMatch[0][1]);
    }, $string);
  }



  public static function str_split_unicode(string $str, int $l = 0) : array
  {
    if ($l > 0) {
      $ret = array();
      $len = mb_strlen($str, "UTF-8");
      for ($i = 0; $i < $len; $i += $l) {
        $ret[] = mb_substr($str, $i, $l, "UTF-8");
      }
      return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
  }
    

};

