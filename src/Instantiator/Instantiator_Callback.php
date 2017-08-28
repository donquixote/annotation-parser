<?php

namespace Donquixote\Annotation\Instantiator;

class Instantiator_Callback implements InstantiatorInterface {

  /**
   * @var callable
   */
  private $callback;

  /**
   * @param callable $callback
   */
  public function __construct($callback) {
    $this->callback = $callback;
  }

  /**
   * @param mixed[] $args
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public function instantiate(array $args, \Reflector $reflector) {
    return call_user_func($this->callback, $args, $reflector);
  }
}
