<?php
namespace Donquixote\Annotation\Value\GenericAnnotation;

/**
 * Interface for annotations where the tag name becomes a property.
 */
interface GenericAnnotationInterface {

  /**
   * @return string
   */
  public function getName();

  /**
   * @return array
   */
  public function getArguments();
}
