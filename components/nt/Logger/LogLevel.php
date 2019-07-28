<?php
declare(strict_types=1);

namespace nt\Logger;

class LogLevel
{
  const EMERGENCY = 7;
  const ALERT = 6;
  const CRITICAL = 5;
  const ERROR = 4;
  const WARNING = 3;
  const NOTICE = 2;
  const INFO = 1;

  const LEVELS = [
    1 => 'INFO',
    2 => 'NOTICE',
    3 => 'WARNING',
    4 => 'ERROR',
    5 => 'CRITICAL',
    6 => 'ALERT',
    7 => 'EMERGENCY'
  ];
}