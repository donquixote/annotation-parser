<?php

namespace Donquixote\Annotation\Tests\Fixtures\Annotation;

use Donquixote\Annotation\Value\ClassedAnnotation\ClassedAnnotationBase;

/**
 * @Annotation
 */
class Hello extends ClassedAnnotationBase {

  /**
   * @var array
   */
  private $values;

  /**
   * @var \Reflector
   */
  private $reflector;

  /**
   * @param array $values
   * @param \Reflector $reflector
   */
  public function __construct(array $values, \Reflector $reflector) {
    $this->values = $values;
    $this->reflector = $reflector;
  }

  /**
   * @return array
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * @return \Reflector
   */
  public function getReflector() {
    return $this->reflector;
  }
}
