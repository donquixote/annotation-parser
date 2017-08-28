<?php

namespace Donquixote\Annotation\Resolver\Object;

use Donquixote\Annotation\Exception\ResolverException;
use Donquixote\Annotation\InstantiatorFinder\InstantiatorFinderInterface;
use Donquixote\Annotation\Resolver\ClassName\ClassNameResolverInterface;

class ObjectResolver_Instantiator implements ObjectResolverInterface {

  /**
   * @var \Donquixote\Annotation\Resolver\ClassName\ClassNameResolverInterface
   */
  private $classNameResolver;

  /**
   * @var \Donquixote\Annotation\InstantiatorFinder\InstantiatorFinderInterface
   */
  private $instantiatorFinder;

  /**
   * @param \Donquixote\Annotation\Resolver\ClassName\ClassNameResolverInterface $classNameResolver
   * @param \Donquixote\Annotation\InstantiatorFinder\InstantiatorFinderInterface $instantiatorFinder
   */
  public function __construct(
    ClassNameResolverInterface $classNameResolver,
    InstantiatorFinderInterface $instantiatorFinder
  ) {
    $this->classNameResolver = $classNameResolver;
    $this->instantiatorFinder = $instantiatorFinder;
  }

  /**
   * @param string $name
   * @param mixed[] $arguments
   *   Arguments which are already resolved.
   * @param \Reflector $reflector
   *
   * @return mixed
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  public function resolve($name, array $arguments, \Reflector $reflector) {

    $class = $this->classNameResolver->resolveClassName($name, $reflector);


    if (NULL !== $instantiator = $this->instantiatorFinder->classGetInstantiator($class)) {
      return $instantiator->instantiate($arguments, $reflector);
    }

    $message = $this->getMessage($class);

    throw new ResolverException($message
      . "\nOriginal annotation name: @$name."
      . "\nAnnotated item: $reflector");
  }

  /**
   * @param string $class
   *
   * @return string
   */
  private function getMessage($class) {

    if (class_exists($class)) {
      return "No instantiator found for class $class.";
    }

    if (interface_exists($class)) {
      return "No instantiator found for interface $class.";
    }

    if (trait_exists($class)) {
      return "No instantiator found for trait $class.";
    }

    return "No instantiator found for non-existing '$class'.";
  }
}
