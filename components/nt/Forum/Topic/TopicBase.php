<?php

declare(strict_types = 1);

namespace nt\Forum\Topic;
use mobile\helpers\ForumHelper;

/**
 * базовый класс темы форума
 * Class TopicBase
 */
class TopicBase
{
  use
    \nt\traits\HasIdNotZero,
    \nt\traits\HasStatusIntNotNull,
    \nt\traits\HasSectionWoman,
    \nt\traits\HasTagIdList,
    \nt\traits\HasCreatedAt,
    \nt\traits\HasUpdatedAt,
    \nt\traits\HasTypeIntNotZero,
    \nt\traits\HasTitle,
    \nt\traits\HasName,
    \nt\traits\HasBody,   // 4713634
    \nt\traits\HasUserId,
    \nt\traits\HasUserName,
    \nt\traits\HasIsAnonymous,
    \nt\traits\HasAnonymousId,
    \nt\traits\HasUserAvatarId,
    \nt\traits\HasUserAgent,
    \nt\traits\HasUserCookie,
    \nt\traits\HasUserIp,
    \nt\traits\HasImageCollection,
    \nt\traits\HasExpertMessage,
    \nt\traits\EntityGetUserHash,
    \nt\traits\HasExpertAnswer;


  /** @var int количество ответов всего (пофигу, видимые-нет), используется для пагинации */
  private $answerCountAll     = null;
  /** @var int количество видимых ответов          */
  private $answerCountVisible = null;
  /** @var int количество ответов за период        */
  private $answerCount3h      = null;
  /** @var int количество ответов за период        */
  private $answerCount12h     = null;
  /** @var int количество ответов за период        */
  private $answerCount1d      = null;
  /** @var int количество ответов за период        */
  private $answerCount3d      = null;
  /** @var int количество ответов за период        */
  private $answerCount7d      = null;
  /** @var int количество ответов за период        */
  private $answerCount30d     = null;
  /** @var int режим комментирования               */
  private $commentMode        = null;
  /** @var string | null суффикс титла (о боже...) */
  private $titlePostfix       = null;
  /** @var string | null поле email из формы создания темы для анонимных пользователей */
  private $mailFromForm       = null;
  /** @var string | null дата последнего ответа в теме */
  private $dateLastComment    = null;
  /** @var int | null дополнительный статус темы   */
  private $statusExt          = null;


  /**
   * @param int $answerCountAll
   * @return $this
   */
  public function setAnswerCountAll(int $answerCountAll) : self
  {
    $this->answerCountAll = $answerCountAll;
    return $this;
  }
  /**
   * @return int
   */
  public function getAnswerCountAll() : int
  {
    return $this->answerCountAll;
  }


  /**
   * @param int $answerCountVisible
   * @return $this
   */
  public function setAnswerCountVisible(int $answerCountVisible) : self
  {
    $this->answerCountVisible = $answerCountVisible;
    return $this;
  }
  /**
   * @return int
   */
  public function getAnswerCountVisible() : int
  {
    return $this->answerCountVisible;
  }


  /**
   * @param int $answerCount3h
   * @return $this
   */
  public function setAnswerCount3h(int $answerCount3h) : self
  {
    $this->answerCount3h = $answerCount3h;
    return $this;
  }
  /**
   * @return int
   */
  public function getAnswerCount3h() : int
  {
    return $this->answerCount3h;
  }


  /**
   * @param int $answerCount12h
   * @return $this
   */
  public function setAnswerCount12h(int $answerCount12h) : self
  {
    $this->answerCount12h = $answerCount12h;
    return $this;
  }
  /**
   * @return int
   */
  public function getAnswerCount12h() : int
  {
    return $this->answerCount12h;
  }


  /**
   * @param int $answerCount1d
   * @return $this
   */
  public function setAnswerCount1d(int $answerCount1d) : self
  {
    $this->answerCount1d = $answerCount1d;
    return $this;
  }
  /**
   * @return int
   */
  public function getAnswerCount1d() : int
  {
    return $this->answerCount1d;
  }


  /**
   * @param int $answerCount3d
   * @return $this
   */
  public function setAnswerCount3d(int $answerCount3d) : self
  {
    $this->answerCount3d = $answerCount3d;
    return $this;
  }
  /**
   * @return int
   */
  public function getAnswerCount3d() : int
  {
    return $this->answerCount3d;
  }


  /**
   * @param int $answerCount7d
   * @return $this
   */
  public function setAnswerCount7d(int $answerCount7d) : self
  {
    $this->answerCount7d = $answerCount7d;
    return $this;
  }
  /**
   * @return int
   */
  public function getAnswerCount7d() : int
  {
    return $this->answerCount7d;
  }


  /**
   * @param int $answerCount30d
   * @return $this
   */
  public function setAnswerCount30d(int $answerCount30d) : self
  {
    $this->answerCount30d = $answerCount30d;
    return $this;
  }
  /**
   * @return int
   */
  public function getAnswerCount30d() : int
  {
    return $this->answerCount30d;
  }


  /**
   * @param int $commentMode
   * @return $this
   */
  public function setCommentMode(int $commentMode) : self
  {
    $this->commentMode = $commentMode;
    return $this;
  }
  /**
   * @return int
   */
  public function getCommentMode() : int
  {
    return $this->commentMode;
  }


  /**
   * @param string | null $titlePostfix
   * @return $this
   */
  public function setTitlePostfix(?string $titlePostfix) : self
  {
    $this->titlePostfix = $titlePostfix;
    return $this;
  }
  /**
   * @return string | null
   */
  public function getTitlePostfix() : ?string
  {
    return $this->titlePostfix;
  }


  public function setMailFromForm(string $email) : self
  {
      $this->mailFromForm = $email;
      return $this;
  }
  public function getMailFromForm() : ?string
  {
      return $this->mailFromForm;
  }


  /**
   * @param string | null $dateLastComment
   * @return $this
   */
  public function setDateLastComment(?string $dateLastComment) : self
  {
    $this->dateLastComment = $dateLastComment;
    return $this;
  }
  /**
   * @return string | null
   */
  public function getDateLastComment() : ?string
  {
    return $this->dateLastComment;
  }


  public function setStatusExt(?int $statusExt) : self
  {
    $this->statusExt = $statusExt;
    return $this;
  }
  public function getStatusExt() : ?int
  {
    return $this->statusExt;
  }

  public function getCountAnswersBySort(string $sort = ''): int
  {
    if($sort == 'new') return $this->getAnswerCount1d();
    if(!in_array($sort, ForumHelper::$thread_list_sort_available)) return $this->getAnswerCountAll();
    $method = 'getAnswerCount'.$sort;
    return $this->$method();
  }


};

