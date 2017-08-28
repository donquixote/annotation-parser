<?php

namespace Donquixote\Annotation\Instantiator;

class Instantiator_StaticMethod implements InstantiatorInterface {

  /**
   * @var \ReflectionMethod
   */
  private $reflectionMethod;

  /**
   * @param \ReflectionMethod $reflectionMethod
   */
  public function __construct(\ReflectionMethod $reflectionMethod) {
    $this->reflectionMethod = $reflectionMethod;
  }

  /**
   * @param mixed[] $args
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public function instantiate(array $args, \Reflector $reflector) {
    return $this->reflectionMethod->invoke(NULL, $args, $reflector);
  }
}
