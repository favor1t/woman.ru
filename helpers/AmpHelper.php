<?php

/**
 * хелпер для работы c AMP
 * Class AmpHelper
 */
class AmpHelper
{

  /**
   * Ставить www/m версии amp разметку или нет
   * @var bool
   */
  private static $isAMP = false;

  /**
   * AMP Url
   * @var string
   */
  private static $ampUrl = '';

  /**
   * @param string $url
   * @return bool
   */
  public static function canShowAmpByUrl(string $url) : bool
  {
    if(preg_match('@/thread/(\d+)/@iU', $url, $arrMatch)) return true;
    if(! preg_match('@^/stars/.+/article/(\d+)/(.+)?@iU', $url, $arrMatch)) return false;
    return self::canShowAmpByPublicationid((int) $arrMatch[1]);
  }
  

  /**
   * @param int $publicationId
   * @return bool
   */
  private static function canShowAmpByPublicationId(int $publicationId) : bool
  {
    NtHelper::init();
    return \nt\Cache::get(self::class, $publicationId, function(int $publicationId)
    {
      $publication = Publication::getById($publicationId);
      return $publication && self::canShowAmpByPublication($publication);
    }, $expire = 60);
  }


  /**
   * @param \Publication | \mobile\models\publications\Publication $publication
   * @return bool
   */
  public static function canShowAmpByPublication($publication) : bool
  {
    $section = $publication->section;
    if(! $section || ! $section->pid || ! $section->parent->isSectionStar()) return false;

    return $publication->isTypeExternalNews();
  }


  /**
   * @param \Publication | \mobile\models\publications\Publication $publication
   */
  public static function registerMetaTagAmpExistsByPublicationIfPossible($publication) : void
  {
    // temporary disabled
    // when will be enable: check protocol, is it must be https?
    return;

    if(! self::canShowAmpByPublication($publication)) return;

    $url = $publication->getSiteUrl($absolute = true);
    $url = str_replace('www.', 'amp.', $url);
    Yii::app()->clientScript->registerLinkTag('amphtml', null, $url);
  }


  /**
   * @return bool
   */
  public static function IsAmp(): bool
  {
    return self::$isAMP;
  }

  /**
   * @param bool $isAmp
   */
  public static function setIsAmp(bool $isAmp): void
  {
    self::$isAMP = $isAmp;
  }

  public static function getLinkRel() : string
  {
    if(self::$isAMP && !empty(self::$ampUrl)) return '<link rel="amphtml" href="'.self::$ampUrl.'">';
    return '';
  }

  /**
   * @return string
   */
  public static function getAmpUrl(): string
  {
    return self::$ampUrl;
  }

  /**
   * @param string $ampUrl
   */
  public static function setAmpUrl(string $ampUrl)
  {
    self::$ampUrl = $ampUrl;
  }



};

