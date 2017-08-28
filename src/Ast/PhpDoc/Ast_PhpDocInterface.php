<?php

namespace Donquixote\Annotation\Ast\PhpDoc;

/**
 * Represents PhpDoc annotations like "@param \stdClass $object The object.".
 */
interface Ast_PhpDocInterface {

  /**
   * @return string
   */
  public function getName();

  /**
   * @return string|null
   *   E.g. "\stdClass $object The object."
   */
  public function getText();
}
