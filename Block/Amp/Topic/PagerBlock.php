<?php
declare(strict_types=1);

namespace Block\Amp\Topic;


/**
 * Class PagerBlock
 * @package Block\Amp\Topic
 */
class PagerBlock extends \BlockBase
{
  use
    \nt\traits\HasTopic,
    \nt\traits\HasPage,
    \nt\traits\HasUrl;
  /**
   * @var int
   */

  private $maxPage = null;

  /**
   * @var bool
   */
  private $isSticky = true;

  /**
   * @return int
   */
  public function getMaxPage(): int
  {
    return $this->maxPage;
  }

  /**
   * @param int $maxPage
   * @return $this
   */
  public function setMaxPage(int $maxPage): self
  {
    $this->maxPage = $maxPage;
    return $this;
  }

  /**
   * @return bool
   */
  public function isFirstPage(): bool
  {
    return $this->page <= 1;
  }

  /**
   * @return bool
   */
  public function isSecondPage(): bool
  {
    return $this->page === 2;
  }

  /**
   * @return bool
   */
  public function isLastPage(): bool
  {
    return $this->page === $this->maxPage;
  }

  /**
   * @return bool
   */
  public function isPenultPage(): bool
  {
    return $this->page === $this->maxPage - 1;
  }

  /**
   * @return bool
   */
  public function hasPreviewPage(): bool
  {
    return $this->page - 1 > 1;
  }

  /**
   * @return bool
   */
  public function hasNextPage(): bool
  {
    return $this->page + 1 < $this->maxPage;
  }

  /**
   * @return bool
   */
  public function isSticky(): bool
  {
    return $this->isSticky;
  }

  /**
   * @param bool $isSticky
   */
  public function setIsSticky(bool $isSticky): self
  {
    $this->isSticky = $isSticky;
    return $this;
  }




};