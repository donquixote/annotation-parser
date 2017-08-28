<?php

namespace Donquixote\Annotation\InstantiatorFinder;

use Donquixote\Annotation\ArgumentsMapper\ArgumentsMapper_Default;
use Donquixote\Annotation\Instantiator\Instantiator_NewInstanceArgs;
use Donquixote\Annotation\Instantiator\Instantiator_ResolverException;

class InstantiatorFinder implements InstantiatorFinderInterface {

  /**
   * @param string $class
   *
   * @return \Donquixote\Annotation\Instantiator\InstantiatorInterface
   */
  public function classGetInstantiator($class) {

    if (!class_exists($class)) {
      if (interface_exists($class)) {
        return new Instantiator_ResolverException(
          "Expected class name, found interface name '$class'.");
      }
      elseif (trait_exists($class)) {
        return new Instantiator_ResolverException(
          "Expected class name, found trait name '$class'.");
      }
      else {
        return new Instantiator_ResolverException(
          "Class '$class' not found.");
      }
    }

    $reflectionClass = new \ReflectionClass($class);

    return new Instantiator_NewInstanceArgs(
      $reflectionClass,
      new ArgumentsMapper_Default());
  }
}
