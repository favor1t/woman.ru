<?php
declare(strict_types=1);

namespace nt\Logger;


class BannerLog extends Logger
{
  private $targetType = LogType::BANNER;

  private $compareProperties = ['name', 'code', 'status', 'body_end', 'rubicon_code', 'geo', 'condition', 'url_condition', 'show_on_index', 'show_in_sprojects', 'show_on_mobile_devices', 'placeholder', 'ad_type', 'sorder', 'sgroup'];

  public function getTargetType()
  {
    return $this->targetType;
  }

  /**
   * @return string
   */
  public function onChange(\Banner $oldObject, \Banner $newObject): self
  {
    $this->setLevel(LogLevel::INFO);
    $this->setTargetId($newObject->id);
    $changedText = [];
    foreach ($this->compareProperties as $prop) {
      if (
        isset($oldObject->$prop, $newObject->$prop) &&
        $oldObject->$prop != $newObject->$prop
      ) {
        $changedText[$prop]['old'] = $oldObject->$prop;
        $changedText[$prop]['new'] = $newObject->$prop;
      }
    }

    if (!empty($changedText)) $this->setExtra(json_encode($changedText));

    return $this;
  }

  public function onDelete(\Banner $banner)
  {
    $this->setLevel(LogLevel::INFO);
    $this->setTargetId($banner->id);
    $this->setExtra(json_encode(['action' => 'delete']));
    return $this;
  }

  public function onAdd(\Banner $banner)
  {
    $this->setLevel(LogLevel::INFO);
    $this->setTargetId($banner->id);
    $this->setExtra(json_encode(['action' => 'add']));
    return $this;
  }

  public function save() : bool
  {
    if(!$this->getExtra()) return false;
    return parent::save();
  }
}