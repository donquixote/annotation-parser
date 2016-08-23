<?php
namespace Donquixote\Annotation\RawAst;

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
