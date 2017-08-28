<?php

namespace Donquixote\Annotation\InstantiatorFinder;

use Donquixote\Annotation\Instantiator\Instantiator_StaticMethod;
use Donquixote\Annotation\Value\ClassedAnnotation\ClassedAnnotationInterface;

class InstantiatorFinder_Annotation implements InstantiatorFinderInterface {

  /**
   * @param string $class
   *
   * @return \Donquixote\Annotation\Instantiator\InstantiatorInterface|null
   */
  public function classGetInstantiator($class) {

    if (!is_a($class, ClassedAnnotationInterface::class, true)) {
      return NULL;
    }

    $method = new \ReflectionMethod($class, 'create');

    return new Instantiator_StaticMethod($method);
  }

}
