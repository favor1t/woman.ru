<?php
declare(strict_types=1);

namespace nt\Forum;

use nt\Forum\Ban\BanBase;

/**
 * Class Ban
 * @package nt\Forum
 */
class Ban extends BanBase
{
  use
    \nt\traits\HasTopic,
    \nt\traits\HasMessage,
    \nt\traits\HasUserId;

  /**
   * @var array
   */
  private $userCookieBans = [];


  /**
   * @param Topic $topic
   * @param Message|null $message
   * @return Ban
   */
  public function init(Topic $topic, Message $message = null): self
  {
    return $this
      ->setForumTopic($topic)
      ->setMessage($message)
      ->setCookieBans(BanBase::getCookiesByTopic($topic));

  }

  /**
   * @return array
   */
  private function getCookieBans(): array
  {
    return $this->userCookieBans;
  }

  /**
   * @param array $userCookieBans
   */
  private function setCookieBans(array $userCookieBans): self
  {
    foreach ($userCookieBans as $userCookie => $messageId)
      $this->pushUserBan($userCookie, $messageId);
    return $this;
  }

  /**
   * @param array $array
   * @return array
   */
  private function pushUserBan(string $userCookie, int $messageId): void
  {
    $this->userCookieBans[$userCookie] = $messageId;
  }

  /**
   * @return array
   */
  public function getResult(): array
  {
    if(!$this->isAuthor()) return [];
    return $this->getCookieBans();
  }

  /**
   * @return array
   */
  public function save(): self
  {
    if(!$this->canChange()) return $this;

    $userCookie = $this->getMessage()->getUserCookie();
    if (!isset($this->getCookieBans()[$userCookie])){
      BanBase::add($this->getForumTopic(), $this->getMessage());
      BanBase::updateCookieCache($this->getForumTopic());
      $this->setCookieBans(array_merge($this->getResult(), [$userCookie => $this->getMessage()->getId()]));
    }

    return $this;
  }


  /**
   * @return Ban
   */
  public function remove(): self
  {
    if(!$this->canChange()) return $this;

    $userCookie = $this->getMessage()->getUserCookie();
    if (isset($this->getCookieBans()[$userCookie])){
      BanBase::delete($this->getForumTopic(), $this->getMessage());
      BanBase::updateCookieCache($this->getForumTopic());
      $userCookies = $this->getCookieBans();
      unset($userCookies[$userCookie]);
      $this->userCookieBans = $userCookies;
    }

    return $this;
  }

  /**
   * @return bool
   */
  private function canChange(): bool
  {
    return $this->isValid() && $this->isAuthor();
  }

  /**
   * @return bool
   */
  private function isAuthor(): bool
  {
    return (
      $this->getForumTopic() &&
      !$this->getForumTopic()->isAnonymous() &&
      !\Yii::app()->user->isGuest &&
      $this->getForumTopic()->getUserId() == \Yii::app()->user->id
    );
  }

  /**
   * @return bool
   */
  private function isValid(): bool
  {
    return $this->hasMessage() && $this->hasTopic() && $this->getForumTopic()->getId() == $this->getMessage()->getTopicId();
  }

  /**
   * @return bool
   */
  private function hasMessage(): bool
  {
    return (bool)$this->getMessage();
  }

  /**
   * @return bool
   */
  private function hasTopic(): bool
  {
    return (bool)$this->getForumTopic();
  }

  public function isBanned(\WebUser $user): bool
  {
    if(!$this->getForumTopic()) return false;

    $banArray = BanBase::getCookiesByTopic($this->getForumTopic());
    if(!$banArray || empty($banArray)) return false;

    if(isset(BanBase::getCookiesByTopic($this->getForumTopic())[$user->cookie->value])) return true;

    return false;
  }
};