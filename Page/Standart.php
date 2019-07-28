<?php

declare(strict_types = 1);

namespace Page;

/**
 * стандартная страница
 * пых отказался работать с названием класса Default =\
 * Class Standart
 * @package Page
 */
class Standart extends Page
{

  /**
   * инициализация вьюхи
   */
  public function init()
  {
    // уперлись в то, что надо копировать в шаблон страницы, вероятно, \woman\protected\views\main_new\layout.php
    // после чего нужно модифицировать используемые виджеты, и только потом подключать новые блоки
    throw new Exception('not supported');

    return parent::init();
  }


};

