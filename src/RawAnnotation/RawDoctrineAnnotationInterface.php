<?php
namespace Donquixote\Annotation\RawAnnotation;

interface RawDoctrineAnnotationInterface {

  /**
   * @return string
   */
  public function getName();

  /**
   * @return array
   */
  public function getArguments();
}
