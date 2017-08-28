<?php

namespace Donquixote\Annotation\Resolver\ClassName;

use Donquixote\Annotation\ContextFinder\ContextFinderInterface;
use Donquixote\Annotation\Exception\ResolverException;

class ClassNameResolver_Default implements ClassNameResolverInterface {

  const FQCN_PATTERN = '@^(\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)+$@';

  /**
   * @var \Donquixote\Annotation\ContextFinder\ContextFinderInterface
   */
  private $contextFinder;

  /**
   * @param \Donquixote\Annotation\ContextFinder\ContextFinderInterface $contextFinder
   */
  public function __construct(ContextFinderInterface $contextFinder) {
    $this->contextFinder = $contextFinder;
  }

  /**
   * @param string $name
   * @param \Reflector $reflector
   *
   * @return string
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  public function resolveClassName($name, \Reflector $reflector) {

    if ('' === $name) {
      throw new ResolverException("Class alias cannot be an empty string.");
    }

    if ('\\' === $name[0]) {

      if ('\\' === $name) {
        throw new ResolverException("Class FQCN cannot be just '\\'.");
      }

      if (!preg_match(self::FQCN_PATTERN, $name)) {
        throw new ResolverException("Invalid FQCN '$name'.");
      }

      return substr($name, 1);
    }

    $context = $this->contextFinder->reflectorGetContext($reflector);

    $imports = $context->getImports();

    if (false === $pos = strpos($name, '\\')) {

      if (array_key_exists($name,$imports)) {
        return $imports[$name];
      }
    }
    else {
      $part  = substr($name, 0, $pos);

      if (array_key_exists($part,$imports)) {
        return $imports[$part] . substr($name, $pos);
      }
    }

    if (NULL !== $namespace = $context->getNamespace()) {
      return $namespace . '\\' . $name;
    }

    return $name;
  }
}
