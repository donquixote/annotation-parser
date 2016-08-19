<?php

namespace Donquixote\Annotation\Value\Identifier;

class Identifier_QcnOrAlias implements IdentifierInterface {

  /**
   * @var string
   */
  private $qcnOrAlias;

  /**
   * @param string $qcnOrAlias
   */
  public function __construct($qcnOrAlias) {
    $this->qcnOrAlias = $qcnOrAlias;
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->qcnOrAlias;
  }
}
