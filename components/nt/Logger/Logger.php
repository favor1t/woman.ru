<?php
declare(strict_types=1);

namespace nt\Logger;


class Logger extends LoggerBase
{
  private static $limit = 100;

  /**
   * @return bool
   */
  public function save(): bool
  {
    $result = (bool)\Db::execute('
      insert into {{main_log}} (target_id, target_type, level, _extra, user_id)
      values (:target_id, :target_type, :level, :_extra, :user_id)',
      [
        ':target_id'    => $this->getTargetId(),
        ':target_type'  => $this->getTargetType(),
        ':level'        => $this->getLevel(),
        ':_extra'       => $this->getExtra(),
        ':user_id'      => $this->getUserId(),
      ]);
    if (!$result) throw new Exception('can not add Log');

    return (bool)$result;
  }

  public function getByIdOrNull(int $id)
  {
    $result = \Db::fetch('
      select id, target_id, target_type, level, _extra, user_id, created_at
      from {{main_log}}
      where id = :id
      limit 1',
      [':id' => $id,]);
    if (!$result) return null;

    return (new self())
      ->setId($result->id)
      ->setUserId($result->user_id)
      ->setExtra((string)$result->_extra)
      ->setLevel($result->level)
      ->setTargetId($result->target_id)
      ->setTargetType($result->target_type)
      ->setCreatedAt($result->created_at);

  }

  public static function getList(array $params = []): array
  {
    $sqlString = self::getSqlByParams($params);
    $result = \Db::fetchAll('
            SELECT id, target_id, target_type, level, _extra, user_id, created_at
            FROM {{main_log}} ' . $sqlString . '
            ');
    if (!$result) return [];
    $logList = [];
    foreach ($result as $item)
      $logList[] = self::createByParams($item);

    return $logList;
  }

  private static function getSqlByParams(array $params = []): string
  {
    $sqlString = self::getSqlByWhere($params);
    $sqlString .= ' ORDER BY id DESC';
    $sqlString .= self::getSqlLimit($params);

    return $sqlString;
  }

  private static function getSqlByWhere($params): string
  {
    $sqlString = 'WHERE 1=1';
    $sqlString .= self::getSqlWhereByTargetId($params);
    $sqlString .= self::getSqlWhereByTargetType($params);
    return $sqlString;
  }

  private static function getSqlWhereByTargetId(array $params): string
  {
    return isset($params['target_id']) ? ' AND target_id = ' . (int)$params['target_id'] : '';
  }

  private static function getSqlWhereByTargetType(array $params): string
  {
    return isset($params['target_type']) ? ' AND target_type = ' . (int)$params['target_type'] : '';
  }

  private static function getSqlLimit(array $params): string
  {
    $sqlOffset = isset($params['page']) && $params['page'] > 1 ? ' OFFSET ' . (($params['page'] - 1) * self::$limit) : '';
    $sqlLimit = ' LIMIT ' . self::$limit;
    return $sqlOffset . $sqlLimit;
  }

  private static function createByParams($result)
  {
    return (new self())
      ->setId($result->id)
      ->setUserId($result->user_id)
      ->setExtra((string)$result->_extra)
      ->setLevel($result->level)
      ->setTargetId($result->target_id)
      ->setTargetType($result->target_type)
      ->setCreatedAt($result->created_at);
  }

  public static function getPages(array $params = []): int
  {
    $sqlString = self::getSqlByWhere($params);
    $result = \Db::fetch('
            SELECT count(id)
            FROM {{main_log}} ' . $sqlString . '
            ');

    return $result->count > 0 ? (int)ceil($result->count / self::$limit) : 1;
  }

  public function getUrl() : ?string
  {
    switch ($this->getTargetType()){
      case LogType::BANNER:       return '/moderator/settings/editBanner/id/'.$this->getTargetId();
      case LogType::SYSTEM_USER:  return '/moderator/user/system/edit/id/'.$this->getTargetId();
    }
    return null;
  }

  public function getName() : ?string
  {
    $name = null;
    $object = LogType::TYPES[$this->getTargetType()]::model()->findByPk($this->getTargetId());
    if($object) $name = $object->name;
    return $name;
  }
}