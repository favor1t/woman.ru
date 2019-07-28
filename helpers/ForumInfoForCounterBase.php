<?php
declare(strict_types=1);

class ForumInfoForCounterBase
{
  /*** @var string */
  private $product      = 'forum';
  /*** @var string */
  private $pageTemplate = '';

  const PAGE_TEMPLATE_FORUM_THREAD      = 1;
  const PAGE_TEMPLATE_THREAD_LIST       = 2;
  const PAGE_TEMPLATE_FORUM_MAIN_PAGE   = 3;

  const PAGE_TEMPLATES_TYPES = [
    self::PAGE_TEMPLATE_FORUM_THREAD     => 'forum_thread',
    self::PAGE_TEMPLATE_THREAD_LIST      => 'thread_list',
    self::PAGE_TEMPLATE_FORUM_MAIN_PAGE  => 'forum_mainpage',
  ];

  /*** @var string */
  private $siteVersion  = '';

  const SITE_VERSTION_MOBILE      = 1;
  const SITE_VERSTION_DESKTOP     = 2;

  const SITE_VERSION_TYPES = [
    self::SITE_VERSTION_MOBILE     => 'mobile',
    self::SITE_VERSTION_DESKTOP    => 'desktop',
  ];

  /*** @var string */
  private $pubDate      = null;
  /*** @var string */
  private $mainSection  = null;
  /*** @var string */
  private $subSection   = null;
  /*** @var int */
  private $itemId       = null;
  /*** @var string */
  private $itemName     = null;
  /*** @var bool */
  private $forumArchive = false;
  /*** @var string */
  private $threadType   = null;

  const THREAD_TYPE_COMMON            = 1;
  const THREAD_TYPE_EXPERT            = 2;
  const THREAD_TYPE_WAITING_ANSWER    = 3;

  const THREAD_TYPES = [
    self::THREAD_TYPE_COMMON          => 'common thread',
    self::THREAD_TYPE_EXPERT          => 'expert thread: has answer',
    self::THREAD_TYPE_WAITING_ANSWER  => 'expert thread: waiting for answer',
  ];

  /*** @var bool */
  private $authorized   = null;
  /*** @var string */
  private $wic          = null;
  /*** @var string */
  private $wil          = null;
  /*** @var string */
  private $wid          = null;


  const DEFAULT_VALUE   = 'not_set';


  /**
   * @return string
   */
  public function getProduct(): string
  {
    return $this->product;
  }

  /**
   * @param string $product
   * @return self
   */
  public function setProduct(string $product): self
  {
    $this->product = $product;
    return $this;
  }

  /**
   * @return string
   */
  public function getPageTemplate(): string
  {
    return $this->pageTemplate ?? self::DEFAULT_VALUE;
  }

  /**
   * @param string $pageTemplate
   * @return self
   */
  public function setPageTemplate(string $pageTemplate): self
  {
    $this->pageTemplate = $pageTemplate;
    return $this;
  }

  /**
   * @return string
   */
  public function getSiteVersion(): string
  {
    return $this->siteVersion ?? self::DEFAULT_VALUE;
  }

  /**
   * @param string $siteVersion
   * @return self
   */
  public function setSiteVersion(string $siteVersion): self
  {
    $this->siteVersion = $siteVersion;
    return $this;
  }

  /**
   * @return string
   */
  public function getPubDate(): string
  {
    return $this->pubDate ?? self::DEFAULT_VALUE;
  }

  /**
   * @param string $pubDate
   * @return self
   */
  public function setPubDate(string $pubDate): self
  {
    $this->pubDate = $pubDate;
    return $this;
  }

  /**
   * @return string
   */
  public function getMainSection(): string
  {
    return $this->mainSection ?? self::DEFAULT_VALUE;
  }

  /**
   * @param string $mainSection
   * @return self
   */
  public function setMainSection(string $mainSection): self
  {
    $this->mainSection = $mainSection;
    return $this;
  }

  /**
   * @return string
   */
  public function getSubSection(): string
  {
    return $this->subSection ?? self::DEFAULT_VALUE;
  }

  /**
   * @param string $subSection
   * @return self
   */
  public function setSubSection(string $subSection): self
  {
    $this->subSection = $subSection;
    return $this;
  }

  /**
   * @return int
   */
  public function getItemId()
  {
    return $this->itemId ?? self::DEFAULT_VALUE;
  }

  /**
   * @param int $itemId
   * @return self
   */
  public function setItemId(int $itemId): self
  {
    $this->itemId = $itemId;
    return $this;
  }

  /**
   * @return string
   */
  public function getItemName(): string
  {
    return $this->itemName ?? self::DEFAULT_VALUE;
  }

  /**
   * @param string $itemName
   * @return self
   */
  public function setItemName(string $itemName): self
  {
    $this->itemName = $itemName;
    return $this;
  }

  /**
   * @return bool
   */
  public function isForumArchive(): bool
  {
    return $this->forumArchive;
  }

  /**
   * @param bool $forumArchive
   * @return self
   */
  public function setForumArchive(bool $forumArchive): self
  {
    $this->forumArchive = $forumArchive;
    return $this;
  }

  /**
   * @return string
   */
  public function getThreadType(): string
  {
    return $this->threadType ?? self::DEFAULT_VALUE;
  }

  /**
   * @param string $threadType
   * @return self
   */
  public function setThreadType(string $threadType): self
  {
    $this->threadType = $threadType;
    return $this;
  }

  /**
   * @return bool
   */
  public function isAuthorized(): bool
  {
    return $this->authorized;
  }

  /**
   * @param bool $authorized
   * @return self
   */
  public function setAuthorized(bool $authorized): self
  {
    $this->authorized = $authorized;
    return $this;
  }

  /**
   * @return string
   */
  public function getWic(): string
  {
    return $this->wic ?? self::DEFAULT_VALUE;
  }

  /**
   * @param string $wic
   * @return self
   */
  public function setWic(string $wic): self
  {
    $this->wic = $wic;
    return $this;
  }

  /**
   * @return string
   */
  public function getWil(): string
  {
    return $this->wil ?? self::DEFAULT_VALUE;
  }

  /**
   * @param string $wil
   * @return self
   */
  public function setWil(string $wil): self
  {
    $this->wil = $wil;
    return $this;
  }

  /**
   * @return string
   */
  public function getWid(): string
  {
    return $this->wid ?? self::DEFAULT_VALUE;
  }

  /**
   * @param string $wid
   * @return self
   */
  public function setWid(string $wid): self
  {
    $this->wid = $wid;
    return $this;
  }

}