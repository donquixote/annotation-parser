<?php

namespace Donquixote\Annotation\Value\GenericAnnotation;

class GenericAnnotation implements GenericAnnotationInterface {

  /**
   * @var string
   */
  private $name;

  /**
   * @var array
   */
  private $arguments;

  /**
   * @param string $name
   * @param array $arguments
   */
  public function __construct($name, array $arguments) {
    $this->name = $name;
    $this->arguments = $arguments;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return array
   */
  public function getArguments() {
    return $this->arguments;
  }

}
