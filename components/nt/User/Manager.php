<?php

declare(strict_types = 1);

namespace nt\User;

/**
 * манагер для работы с юзерами
 * Class Manager
 */
class Manager extends \nt\User\Mapper
{


  /**
   * создает и возвращает пароль
   * @param int $length
   * @return string
   */
  public static function createPassword(int $length = 6) : string
  {
    $arrConst = str_split('bdgkmnprst');
    $arrVowel = str_split('aeiu');

    $password = '';
    while(strlen($password) < $length)
    {
      $password .= $arrConst[mt_rand(0, count($arrConst) - 1)];
      $password .= $arrVowel[mt_rand(0, count($arrVowel) - 1)];
    }

    return substr($password, $offset = 0, $length);
  }


};

