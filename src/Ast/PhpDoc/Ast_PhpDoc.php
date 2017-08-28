<?php

namespace Donquixote\Annotation\Ast\PhpDoc;

/**
 * Represents PhpDoc annotations like "@param \stdClass $object The object.".
 */
class Ast_PhpDoc implements Ast_PhpDocInterface {

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
   *   E.g. "\stdClass $object The object."
   */
  public function getText() {
    return $this->text;
  }

}
