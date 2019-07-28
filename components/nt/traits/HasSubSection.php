<?php
declare(strict_types=1);

namespace nt\traits;

/**
 * Trait HasSubSection
 * @package nt\traits
 */
trait HasSubSection
{
  /**
   * @var null
   */
  private $subSection = null;

  /**
   * @return mixed | null
   */
  public function getSubSection()
  {
    return $this->subSection;
  }

  /**
   * @param mixed $subSection
   */
  public function setSubSection($subSection)
  {
    $this->subSection = $subSection;
    return $this;
  }
}