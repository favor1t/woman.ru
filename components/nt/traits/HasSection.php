<?php
declare(strict_types=1);

namespace nt\traits;


/**
 * Trait HasSection
 * @package nt\traits
 */
trait HasSection
{
  /**
   * @var null
   */
  private $section = null;

  /**
   * @return mixed | null
   */
  public function getSection()
  {
    return $this->section;
  }

  /**
   * @param mixed $section
   */
  public function setSection($section)
  {
    $this->section = $section;
    return $this;
  }
}