<?php

namespace Donquixote\Annotation\Instantiator;

interface InstantiatorInterface {

  /**
   * @param mixed[] $args
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public function instantiate(array $args, \Reflector $reflector);

}
