<?php
declare(strict_types=1);

namespace Block\Amp\Topic;


use mobile\models\menu\MenuItem;

class SidebarBlock extends \BlockBase
{
  /**
   * @var array
   */
  private $menuItems = [];

  public function init()
  {
    $this->setMenuItems(MenuItem::getCompleteMenuItems($current = null, $for_forum = true, $menuId = MenuItem::SIDE_MENU));
    return parent::init();
  }

  /**
   * @return array
   */
  public function getMenuItems(): array
  {
    return $this->menuItems;
  }

  /**
   * @param array $menuItems
   */
  public function setMenuItems(array $menuItems)
  {
    $this->menuItems = $menuItems;
  }

};