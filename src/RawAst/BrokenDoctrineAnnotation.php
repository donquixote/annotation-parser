<?php

namespace Donquixote\Annotation\RawAst;

class BrokenDoctrineAnnotation {

  /**
   * @var string
   */
  private $name;

  /**
   * @var string
   */
  private $text;

  /**
   * @param string $name
   * @param string $text
   */
  public function __construct($name, $text) {
    $this->name = $name;
    $this->text = $text;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getText() {
    return $this->text;
  }

}
