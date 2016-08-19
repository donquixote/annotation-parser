<?php

namespace Donquixote\Annotation\Value\Identifier;

class Identifier_ClassAliasConstant implements IdentifierInterface {

  /**
   * @var string
   */
  private $classAlias;

  /**
   * @var string
   */
  private $constantName;

  /**
   * @param string $classAlias
   *   Qualified class name.
   * @param string $constantName
   */
  public function __construct($classAlias, $constantName) {
    $this->classAlias = $classAlias;
    $this->constantName = $constantName;
  }

  /**
   * @return string
   */
  public function getClassAlias() {
    return $this->classAlias;
  }

  /**
   * @return string
   */
  public function getConstantName() {
    return $this->constantName;
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->classAlias . '::' . $this->constantName;
  }

}
