<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * сахар для вызовов методов манагера сущности
 * Trait EntitySaveInCache
 */
trait EntitySaveInCache
{

  /**
   * обновляет сущность в кеше
   * @return $this
   */
  public function saveInCache() : self
  {
    // @TODO: интересно, насколько быстро это работает
    return call_user_func([ get_called_class().'\Manager', 'saveInCache', ], $this);
  }

};


