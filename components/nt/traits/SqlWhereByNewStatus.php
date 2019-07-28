<?php
declare(strict_types = 1);

namespace nt\traits;

trait SqlWhereByNewStatus
{
  /**
   * @param array $arrParam
   * @return string | null
   */
  private static function getSqlWhereByNewStatus(array $arrParam) : ?string
  {
    if(isset($arrParam['status'])) return 'status = ' . (int) $arrParam['status'];
    return null;
  }
}