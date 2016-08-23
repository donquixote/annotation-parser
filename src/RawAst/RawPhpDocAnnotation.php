<?php

namespace Donquixote\Annotation\RawAst;

class RawPhpDocAnnotation {

  /**
   * @var string
   */
  private $name;

  /**
   * @var string|null
   */
  private $text;

  /**
   * @param string $name
   * @param string|null $text
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
   * @return string|null
   */
  public function getText() {
    return $this->text;
  }

}
