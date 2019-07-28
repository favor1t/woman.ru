<?php
declare(strict_types=1);

class ForumInfoForCounter extends ForumInfoForCounterBase
{
  use Singleton;
  /**
   * ForumInfoForCounterBase constructor.
   */
  private function __construct()
  {
    $request = Yii::app()->request;

    $wic = $request->getParam('wic');
    if ($wic) $this->setWic($wic);
    $wid = $request->getParam('wid');
    if ($wid) $this->setWid($wid);
    $wil = $request->getParam('wil');
    if ($wil) $this->setWil($wil);
    $this->setAuthorized(false);
    if(Yii::app()->user->id > 0) $this->setAuthorized(true);
  }

  /**
   * @param Section $section
   * @param Section $subSection
   * @return ForumInfoForCounter
   */
  public function setResultBySections(Section $section, Section $subSection): self
  {

  }

  /**
   * @return string
   */
  private function getResultForGA(): string
  {
    return JSON::encode(array_merge($this->getDefaultDimensionsByGA(),$this->getValuesForGA()));
  }

  private function getResultForYM(): string
  {
    return JSON::encode($this -> getValuesForYM());
  }

  /**
   * @return array
   */
  private function getDefaultDimensionsByGA(): array
  {
    return [
      "allow_display_features"  => true,
      "transport_type"          =>  "beacon",
      "custom_map"              => [
        "dimension1"  => "Page Template",
        "dimension2"  => "Section",
        "dimension3"  => "Publication Date",
        "dimension4"  => "Item ID",
        "dimension5"  => "Item Name",
        "dimension6"  => "Site Version",
        "dimension7"  => "WIC: Campaign",
        "dimension8"  => "WIC: Location",
        "dimension10" => "Thread Type",
        "dimension11" => "WIC: Description",
        "dimension12" => "Authorization",
      ]
    ];
  }

  /**
   * @return array
   */
  private function getValuesForGA(): array
  {
    return [
    "Page Template"     => $this->getPageTemplate(),
    "Section"           => $this->getMainSection() ? $this->getMainSection() :  ($this->getSubSection() ?? self::DEFAULT_VALUE) ,
    "Publication Date"  => $this->getPubDate(),
    "Item ID"           => $this->getItemId(),
    "Item Name"         => $this->getItemName() ?? self::DEFAULT_VALUE,
    "Site Version"      => $this->getSiteVersion() ?? self::DEFAULT_VALUE,
    "WIC: Campaign"     => $this->getWic() ?? self::DEFAULT_VALUE,
    "WIC: Location"     => $this->getWil() ?? self::DEFAULT_VALUE,
    "Thread Type"       => $this->getThreadType() ?? self::DEFAULT_VALUE,
    "WIC: Description"  => $this->getWid() ?? self::DEFAULT_VALUE,
    "Authorization"     => $this->isAuthorized(),
    ];
  }


  /**
   * @return array
   */
  private function getValuesForYM(): array
  {
    return [
      "WIC: Campaign"     => $this->getWic() ?? self::DEFAULT_VALUE,
      "WIC: Location"     => $this->getWil() ?? self::DEFAULT_VALUE,
      "WIC: Description"  => $this->getWid() ?? self::DEFAULT_VALUE,
      "Product"           => $this->getProduct() ?? self::DEFAULT_VALUE,
      "Page Template"     => $this->getPageTemplate(),
      "Site Version"      => $this->getSiteVersion() ?? self::DEFAULT_VALUE,
      "Publication Date"  => $this->getPubDate(),
      "Section"           => $this->getMainSection() ? $this->getMainSection() :  ($this->getSubSection() ?? self::DEFAULT_VALUE) ,
      "Item ID"           => $this->getItemId(),
      "Item Name"         => $this->getItemName() ?? self::DEFAULT_VALUE,
      "Forum Archive"     => $this->isForumArchive() ?? self::DEFAULT_VALUE,
      "Thread Type"       => $this->getThreadType() ?? self::DEFAULT_VALUE,
      "Authorized"        => $this->isAuthorized(),
    ];
  }

  /**
   * @return string
   */
  public static function getHashByGA(): string
  {
    return self::getHash().'GA';
  }

  public static function getHashByYM(): string
  {
    return self::getHash().'YM';
  }

  private static function getHash(): string
  {
    return __CLASS__;
  }

  private static function replaceByGA(string $html)
  {
    return str_replace(self::getHashByGA(), self::getInstance()->getResultForGA(), $html);
  }

  private static function replaceByYM(string $html)
  {
    return str_replace(self::getHashByYM(), self::getInstance()->getResultForYM(), $html);
  }

  public static function replace(string $html): string
  {
    $html = self::replaceByGA($html);
    $html = self::replaceByYM($html);
    return $html;

  }


}