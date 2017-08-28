<?php

namespace Donquixote\Annotation\InstantiatorFinder;

use Donquixote\Annotation\Instantiator\InstantiatorInterface;

class InstantiatorFinder_FixedMap implements InstantiatorFinderInterface {

  /**
   * @var \Donquixote\Annotation\Instantiator\InstantiatorInterface[]
   */
  private $instantiators;

  /**
   * @param \Donquixote\Annotation\Instantiator\InstantiatorInterface[] $instantiators
   */
  public function __construct(array $instantiators) {
    $this->instantiators = $instantiators;
  }

  /**
   * @param string $class
   * @param \Donquixote\Annotation\Instantiator\InstantiatorInterface $instantiator
   *
   * @return static
   */
  public function withKnownInstantiator($class, InstantiatorInterface $instantiator) {
    $clone = clone $this;
    $clone->instantiators[$class] = $instantiator;
    return $clone;
  }

  /**
   * @param string $class
   *
   * @return \Donquixote\Annotation\Instantiator\InstantiatorInterface|null
   */
  public function classGetInstantiator($class) {
    return array_key_exists($class, $this->instantiators)
      ? $this->instantiators[$class]
      : NULL;
  }

}
