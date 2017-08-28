<?php

namespace Donquixote\Annotation\Reflector;

interface CustomReflectorInterface extends \Reflector {

  /**
   * @return string|null
   */
  public function getCacheId();

  /**
   * @return string|null
   */
  public function getNamespace();

  /**
   * @return string[]
   *   Format: $[$alias] = $classOrNamespace
   */
  public function getImports();

}
