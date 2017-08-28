<?php

namespace Donquixote\Annotation\Context;

class Context implements ContextInterface {

  /**
   * @var null|string
   */
  private $namespace;

  /**
   * @var string[]
   */
  private $imports;

  /**
   * @param string|null $namespace
   * @param string[] $imports
   */
  public function __construct($namespace, $imports) {
    $this->namespace = $namespace;
    $this->imports = $imports;
  }

  /**
   * @return string|null
   */
  public function getNamespace() {
    return $this->namespace;
  }

  /**
   * @return string[]
   *   Format: $[$alias] = $class
   */
  public function getImports() {
    return $this->imports;
  }
}
