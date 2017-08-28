<?php

namespace Donquixote\Annotation\Value\ClassedAnnotation;

/**
 * Interface for annotations where the tag name becomes the class name.
 */
interface ClassedAnnotationInterface {

  /**
   * @param array $values
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public static function create(array $values, \Reflector $reflector);

}
