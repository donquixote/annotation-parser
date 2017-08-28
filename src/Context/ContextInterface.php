<?php

namespace Donquixote\Annotation\Context;

/**
 * The "context" represents where an annotation was found.
 */
interface ContextInterface {

  /**
   * @return string|null
   */
  public function getNamespace();

  /**
   * @return string[]
   *   Format: $[$alias] = $class
   */
  public function getImports();

  /**
   * @return mixed
   */
  # public function getClassName();

}
