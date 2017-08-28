<?php

namespace Donquixote\Annotation\Value\ClassedAnnotation;

abstract class ClassedAnnotationBase implements ClassedAnnotationInterface {

  /**
   * @param array $values
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public static function create(array $values, \Reflector $reflector) {
    return new static($values, $reflector);
  }

  /**
   * @param array $values
   * @param \Reflector $reflector
   */
  abstract public function __construct(array $values, \Reflector $reflector);
}
