<?php
declare(strict_types = 1);

namespace nt\traits;

trait SqlWhereByTopicId
{
  /**
   * @param array $arrParam
   * @return string | null
   */
  private static function getSqlWhereByTopicId(array $arrParam) : ?string
  {
    if(isset($arrParam['topicId']) && $arrParam['topicId'] > 0) return 'thread_id = ' . (int)$arrParam['topicId'];
    return null;
  }
}