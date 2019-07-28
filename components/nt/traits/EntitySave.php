<?php

declare(strict_types = 1);

namespace nt\traits;

/**
 * трейт "метод save() в сущностях"
 * Class EntitySave
 */
trait EntitySave
{

  public function save()
  {
    return call_user_func([ get_called_class().'\Manager', 'save', ], $this);
  }

};

