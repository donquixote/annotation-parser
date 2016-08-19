<?php

namespace Donquixote\Annotation\Value\Identifier;

class Identifier_Fqcn implements IdentifierInterface {

  /**
   * @var string
   */
  private $qcn;

  /**
   * @param string $fqcn
   *
   * @return Identifier_Fqcn
   */
  public static function createFromFqcn($fqcn) {
    if ('' === $fqcn) {
      throw new \InvalidArgumentException("Fqcn cannot be empty.");
    }
    if ('\\' !== $fqcn[0]) {
      throw new \InvalidArgumentException("Fqcn must begin with '\\'.");
    }
    return new self(substr($fqcn, 1));
  }

  /**
   * @param string $qcn
   *
   * @return Identifier_Fqcn
   */
  public static function createFromQcn($qcn) {
    if ('' === $qcn) {
      throw new \InvalidArgumentException("Qcn cannot be empty.");
    }
    if ('\\' !== $qcn[0]) {
      throw new \InvalidArgumentException("Fqcn must not begin with '\\'.");
    }
    return new self($qcn);
  }

  /**
   * @param string $qcn
   */
  private function __construct($qcn) {
    $this->qcn = $qcn;
  }

  /**
   * @return string
   */
  public function getFqcn() {
    return '\\' . $this->qcn;
  }

  /**
   * @return string
   */
  public function getQcn() {
    return $this->qcn;
  }

  /**
   * @return string
   */
  public function __toString() {
    return '\\' . $this->qcn;
  }

}
