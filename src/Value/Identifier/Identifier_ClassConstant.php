<?php

namespace Donquixote\Annotation\Value\Identifier;

class Identifier_ClassConstant implements IdentifierInterface {

  /**
   * @var string
   */
  private $className;

  /**
   * @var string
   */
  private $constantName;

  /**
   * @param string $fqcn
   * @param string $constantName
   *
   * @return self
   */
  public static function createFromFqcn($fqcn, $constantName) {
    if ('' === $fqcn) {
      throw new \InvalidArgumentException("Fqcn cannot be empty.");
    }
    if ('\\' !== $fqcn[0]) {
      throw new \InvalidArgumentException("Fqcn must begin with '\\'.");
    }
    return new self(substr($fqcn, 1), $constantName);
  }

  /**
   * @param string $className
   *   Qualified class name.
   * @param string $constantName
   */
  private function __construct($className, $constantName) {
    $this->className = $className;
    $this->constantName = $constantName;
  }

  /**
   * @return string
   */
  public function getClassName() {
    return $this->className;
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
    return $this->className . '::' . $this->constantName;
  }

  /**
   * @return string
   */
  public function getFqcn() {
    return '\\' . $this->className . '::' . $this->constantName;
  }

}
