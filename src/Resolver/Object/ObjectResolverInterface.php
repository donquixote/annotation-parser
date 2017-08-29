<?php

namespace Donquixote\Annotation\Resolver\Object;

interface ObjectResolverInterface {

  /**
   * @param string $name
   *   Original annotation tag name, before any alias replacement.
   * @param mixed[] $arguments
   *   Arguments which are already resolved.
   * @param \Reflector $reflector
   *   Class, method or property where the annotation was found.
   *   It could also be an (empty) CustomReflector, for cases where we only know
   *   the annotation itself, but not where it was found.
   *
   * @return mixed
   *
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  public function resolve($name, array $arguments, \Reflector $reflector);

}
