<?php

namespace Donquixote\Annotation\Reflector;

trait CustomReflectorTrait {

  /**
   * @var string|null
   */
  private $stringRepresentation;

  /**
   * @var string|null
   */
  private $cacheId;

  /**
   * @var string|null
   */
  private $namespace;

  /**
   * @var string[]
   */
  private $imports = [];

  /**
   * @param string $stringRepresentation
   */
  public function withStringRepresentation($stringRepresentation) {
    $this->stringRepresentation = $stringRepresentation;
  }

  /**
   * @param string $cacheId
   *
   * @return static
   */
  public function withCacheId($cacheId) {
    $clone = clone $this;
    $clone->cacheId = $cacheId;
    return $clone;
  }

  /**
   * @param string $namespace
   *
   * @return static
   */
  public function withNamespace($namespace) {
    $clone = clone $this;
    $clone->namespace = $namespace;
    return $clone;
  }

  /**
   * @param string[] $imports
   *
   * @return static
   */
  public function withImports(array $imports) {
    $clone = clone $this;
    $clone->imports = $imports;
    return $clone;
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->getStringRepresentation();
  }

  /**
   * @return string
   */
  private function getStringRepresentation() {

    if (NULL !== $this->stringRepresentation) {
      return (string)$this->stringRepresentation;
    }

    if (NULL !== $cacheId = $this->getCacheId()) {
      return $cacheId;
    }

    return static::class;
  }

  /**
   * @return string|null
   */
  public function getCacheId() {
    return $this->cacheId;
  }

  /**
   * @return string|null
   */
  public function getNamespace() {
    return $this->namespace;
  }

  /**
   * @return string[]
   *   Format: $[$alias] = $classOrNamespace
   */
  public function getImports() {
    return $this->imports;
  }

}
