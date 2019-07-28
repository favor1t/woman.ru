<?php
declare(strict_types = 1);

namespace nt\traits;


trait HasPage
{
  /**
   * @var int
   */
  private $page = null;
  /**
   * @return int
   */
  public function getPage(): int
  {
    return $this->page;

  }

  /**
   * @param int $page
   */
  public function setPage(int $page): self
  {
    $this->page = $page;
    return $this;
  }




}