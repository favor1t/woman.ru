<?php
declare(strict_types=1);

namespace nt\Logger;


class SystemUserLog extends Logger
{
  private $targetType = LogType::SYSTEM_USER;

  private $compareProperties = ['name', 'status', 'login', 'role', 'email', 'icq', 'skype', 'phone', 'password', 'section_id'];

  public function getTargetType()
  {
    return $this->targetType;
  }

  public function onChange(\SystemUser $oldUser, \SystemUser $newUser): self
  {
    return $this->getChanges($oldUser, $newUser);
  }

  private function getChanges(\SystemUser $oldUser, \SystemUser $newUser) : self
  {
    $this->setLevel(LogLevel::INFO);
    $this->setTargetId($newUser->id);
    $changedText = [];
    foreach ($this->compareProperties as $prop) {
      if (
        isset($oldUser->$prop, $newUser->$prop) &&
        $prop != 'password' &&
        $oldUser->$prop != $newUser->$prop
      ) {
        $changedText[$prop]['old'] = $oldUser->$prop;
        $changedText[$prop]['new'] = $newUser->$prop;
      }
    }

    if (!empty($changedText)) $this->setExtra(json_encode($changedText));

    return $this;
  }

  public function onAdd(\SystemUser $user): self
  {
    $this->setLevel(LogLevel::INFO);
    $this->setTargetId($user->id);
    $this->setExtra(json_encode(['action' => 'add']));
    return $this;
  }

  public function save(): bool
  {
    if (!$this->getExtra()) return false;
    return parent::save();
  }
}