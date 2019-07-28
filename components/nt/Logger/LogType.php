<?php
declare(strict_types=1);

namespace nt\Logger;


class LogType
{
  const BANNER = 1;
  const SYSTEM_USER = 2;

  const TYPES = [
    self::BANNER        => 'Banner',
    self::SYSTEM_USER   => 'SystemUser',

  ];

}