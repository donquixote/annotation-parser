<?php

namespace Donquixote\Annotation\Resolver\ClassName;

interface ClassNameResolverInterface {

  /**
   * @param string $name
   * @param \Reflector $reflector
   *
   * @return string
   */
  public function resolveClassName($name, \Reflector $reflector);

}
