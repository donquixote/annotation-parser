<?php
namespace Donquixote\Annotation\Value\DoctrineAnnotation;

interface DoctrineAnnotationInterface {

  /**
   * @return string
   */
  public function getName();

  /**
   * @return array
   */
  public function getArguments();
}
