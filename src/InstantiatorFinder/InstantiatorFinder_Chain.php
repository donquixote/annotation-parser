<?php

namespace Donquixote\Annotation\InstantiatorFinder;

class InstantiatorFinder_Chain implements InstantiatorFinderInterface {

  /**
   * @var \Donquixote\Annotation\InstantiatorFinder\InstantiatorFinderInterface[]
   */
  private $factories;

  /**
   * @param \Donquixote\Annotation\InstantiatorFinder\InstantiatorFinderInterface[] $factories
   */
  public function __construct(array $factories) {
    $this->factories = $factories;
  }

  /**
   * @param string $class
   *
   * @return \Donquixote\Annotation\Instantiator\InstantiatorInterface|null
   */
  public function classGetInstantiator($class) {

    foreach ($this->factories as $factory) {
      if (NULL !== $instantiator = $factory->classGetInstantiator($class)) {
        return $instantiator;
      }
    }

    return NULL;
  }
}
