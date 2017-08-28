<?php

namespace Donquixote\Annotation\Resolver\Object;

use Donquixote\Annotation\Value\GenericAnnotation\GenericAnnotation;

class ObjectResolver_GenericAnnotationClass implements ObjectResolverInterface {

  /**
   * @param string $name
   * @param mixed[] $arguments
   *   Arguments which are already resolved.
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public function resolve($name, array $arguments, \Reflector $reflector) {
    return new GenericAnnotation($name, $arguments);
  }
}
