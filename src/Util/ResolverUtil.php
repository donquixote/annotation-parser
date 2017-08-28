<?php

namespace Donquixote\Annotation\Util;

use Donquixote\Annotation\Exception\ResolverException;

class ResolverUtil {

  /**
   * @param string $class
   *
   * @return \ReflectionClass
   *
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  public static function requireReflectionClass($class) {

    if (!class_exists($class)) {
      if (interface_exists($class)) {
        throw new ResolverException("Expected class name, found interface name '$class'.");
      }
      elseif (trait_exists($class)) {
        throw new ResolverException("Expected class name, found trait name '$class'.");
      }
      else {
        throw new ResolverException("Class '$class' not found.");
      }
    }

    return new \ReflectionClass($class);
  }
}
