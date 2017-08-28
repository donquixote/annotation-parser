<?php

namespace Donquixote\Annotation\Resolver\Object;

interface ObjectResolverInterface {

  /**
   * @param string $name
   * @param mixed[] $arguments
   *   Arguments which are already resolved.
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public function resolve($name, array $arguments, \Reflector $reflector);

}
