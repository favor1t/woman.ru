<?php
declare(strict_types=1);

namespace nt;

use nt\Forum\Topic;

class SPBlock
{
  const TARGET_TYPE_TAG = 1;
  const TARGET_TYPE_TOPIC = 2;

  const TARGET_TYPES = [
    self::TARGET_TYPE_TAG => 'TAG',
    self::TARGET_TYPE_TOPIC => 'TOPIC',
  ];

  const STATUS_ACTIVE = 1;
  const STATUS_HIDDEN = 2;

  const STATUSES = [
    self::STATUS_ACTIVE => 'ACTIVE',
    self::STATUS_HIDDEN => 'HIDDEN',
  ];

  private $id = null;
  private $status = null;
  private $htmlWww = '';
  private $htmlMobile = '';
  private $targets = [];

  /**
   * @return int
   */
  public function getId(): ?int
  {
    return $this->id;
  }


  /**
   * @param int $id
   * @return SPBlock
   */
  public function setId(int $id): self
  {
    $this->id = $id;
    return $this;
  }

  /**
   * @return int
   */
  public function getStatus(): ?int
  {
    return $this->status;
  }

  /**
   * @param null $status
   * @return SPBlock
   */
  public function setStatus(int $status)
  {
    $this->status = $status;
    return $this;
  }


  /**
   * @return string
   */
  public function getHtmlWww(): string
  {
    return $this->htmlWww;
  }

  /**
   * @param string $htmlWww
   * @return SPBlock
   */
  public function setHtmlWww(string $htmlWww): self
  {
    $this->htmlWww = $htmlWww;
    return $this;
  }

  /**
   * @return string
   */
  public function getHtmlMobile(): string
  {
    return $this->htmlMobile;
  }

  /**
   * @param string $htmlMobile
   * @return SPBlock
   */
  public function setHtmlMobile(string $htmlMobile)
  {
    $this->htmlMobile = $htmlMobile;
    return $this;
  }

  /**
   * @return array
   */
  public function getTargets(): array
  {
    return $this->targets;
  }

  /**
   * @param array $targets
   */
  public function setTargets(array $targets)
  {
    $this->targets = $targets;
    return $this;
  }


  /**
   * @return int
   */
  public function delete(): void
  {
    if ($this->getId() > 0) {
      $this->setTargets([]);
      $this->saveTarget();
      \Db::execute('
      DELETE FROM {{sp_block}}
      WHERE id = :id',
        [
          ':id' => $this->getId(),
        ]);

      $this->onChange();
    }
  }

  public function save(): void
  {
    if ($this->getId() > 0) $this->update();
    else $this->insert();
  }

  private function update(): void
  {
    \Db::execute('
        UPDATE {{sp_block}}
        SET 
          html_www    = :html_www,
          html_mobile = :html_mobile,
          status      = :status
        WHERE id = :id',
      [
        'html_www' => $this->getHtmlWww(),
        'html_mobile' => $this->getHtmlMobile(),
        'status' => $this->getStatus(),
        ':id' => $this->getId(),
      ]);
    $this->saveTarget();
    $this->onChange();
  }

  private function insert(): void
  {
    \Db::execute('
      INSERT INTO {{sp_block}} (html_www, html_mobile, status)
      VALUES (:html_www, :html_mobile, :status)',
      [
        'html_www' => $this->getHtmlWww(),
        'html_mobile' => $this->getHtmlMobile(),
        'status' => $this->getStatus(),
      ]);

    $this->onChange();
  }


  private function onChange()
  {
    \nt\Cache::set(self::getCacheByAll(), $entityId = null, $value = self::getRealAll());
    $array = self::getAll();
    foreach ($array as $block) \nt\Cache::set(self::getCacheByAll(['id' => $block->getId()]), $entityId = null, $value = self::getRealAll(['id' => $block->getId()]));
    \nt\Cache::set(self::getCacheByTargets(self::TARGET_TYPE_TAG), $entityId = null, $value = self::getRealTargetsByType(self::TARGET_TYPE_TAG));
    \nt\Cache::set(self::getCacheByTargets(self::TARGET_TYPE_TOPIC), $entityId = null, $value = self::getRealTargetsByType(self::TARGET_TYPE_TOPIC));
  }


  private function saveTarget(): void
  {
    \Db::execute('
      DELETE FROM {{sp_block_targets}} WHERE sp_block_id = :sp_block_id',
      [
        ':sp_block_id' => $this->getId(),
      ]);

    if (!empty($this->getTargets()))
      foreach ($this->getTargets() as $targetTypeId => $targets)
        if (is_array($targets))
          foreach ($targets as $targetId)
            if($targetId > 0)
            \Db::execute('
              INSERT INTO {{sp_block_targets}} (target_id, sp_block_id, target_type_id)
              VALUES (:target_id, :sp_block_id, :target_type_id)',
              [
                'target_id' => $targetId,
                'sp_block_id' => $this->getId(),
                'target_type_id' => $targetTypeId,
              ]);

    $this->onChange();
  }

  private static function getCacheByAll(array $array = []): string
  {
    return __CLASS__ . '~' . implode("~", $array);
  }

  public static function getAll(array $params = []): array
  {
    return \nt\Cache::get(self::getCacheByAll($params), $entityId = null, function () use ($params) {
      return self::getRealAll($params);
    });
  }

  private static function getRealAll(array $params = []): array
  {
    $result = [];
    $sqlWhere = '';
    $sqlWhere .= self::getSqlById($params);
    $sqlWhere .= self::getSqlByStatus($params);
    $arr = \Db::fetchAll('
        SELECT id, status, html_www, html_mobile
        FROM   {{sp_block}} 
        WHERE 1 = 1 ' .
      $sqlWhere
    );
    if (!is_array($arr)) $arr = [];

    foreach ($arr as $block) {
      $spBlock = (new self())
        ->setId((int)$block->id)
        ->setStatus((int)$block->status)
        ->setHtmlWww((string)$block->html_www)
        ->setHtmlMobile((string)$block->html_mobile);
      $spBlock->setTargets($spBlock->getRealTargets());
      $result[] = $spBlock;
    }

    return $result;
  }

  private static function getCacheByTargets(int $targetType): string
  {
    return __CLASS__ . '~' . $targetType;
  }


  private static function getTargetsByType(int $targetType = 2)
  {
    return \nt\Cache::get(self::getCacheByTargets($targetType), $entityId = null, function () use ($targetType) {
      return self::getRealTargetsByType($targetType);
    });
  }

  private static function getRealTargetsByType(int $targetType): array
  {
    $result = [];
    $array = \Db::fetchAll('
        SELECT target_id, target_type_id, sp_block_id
        FROM {{sp_block_targets}}
        WHERE target_type_id = ' . $targetType
    );
    foreach ($array as $target) $result[$target->target_id] = $target->sp_block_id;
    return $result;
  }

  private static function getSqlById(array $params): string
  {
    $sql = '';
    if (isset($params['id']) && $params['id'] > 0)
      $sql = ' AND id = ' . (int) $params['id'];
    return $sql;
  }
  private static function getSqlByStatus(array $params): string
  {
    $sql = '';
    if (isset($params['status']) && $params['status'] > 0)
      $sql = ' AND status = ' . (int) $params['status'];
    return $sql;
  }

  private function getRealTargets(): array
  {
    $result = [];
    if ($this->getId() > 0) {
      $array = \Db::fetchAll('
        SELECT target_id, target_type_id
        FROM {{sp_block_targets}}
        WHERE sp_block_id = ' . $this->getId()
      );
      foreach ($array as $target) {
        $result[$target->target_type_id][] = $target->target_id;
      }
    }
    return $result;
  }

  public static function getList($params = []): array
  {
    $sqlWhere = '';

    if (isset($params['id']) && $params['id'] > 0)
      $sqlWhere .= ' AND id = ' . $params['id'];

    return self::prepareList(\Db::fetchAll('
        SELECT id, status, html_www, html_mobile
        FROM   {{sp_block}}
        WHERE 1=1
        ' . $sqlWhere . '
        ORDER BY id DESC
    '));

  }

  private static function prepareList(array $result): array
  {
    $arr = [];
    foreach ($result as $spBlock) {
      $spBlock = (new self())
        ->setId((int)$spBlock->id)
        ->setStatus((int)$spBlock->status)
        ->setHtmlWww((string)$spBlock->html_www)
        ->setHtmlMobile((string)$spBlock->html_mobile);
      $spBlock->setTargets($spBlock->getRealTargets());
      $arr[$spBlock->id] = $spBlock;
    }
    return $arr;
  }

  public static function getBlockByTopic(Topic $topic): ?self
  {
    $result = null;
    if (isset(self::getTargetsByType(self::TARGET_TYPE_TOPIC)[$topic->getId()])) {
      $result = current(self::getAll(['id' => (int) self::getTargetsByType(self::TARGET_TYPE_TOPIC)[(int)$topic->getId()], 'status' => self::STATUS_ACTIVE]));
      if(!is_object($result)) $result = null;
    }

    return $result;
  }
}