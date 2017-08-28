<?php

namespace Donquixote\Annotation\InstantiatorFinder;

class InstantiatorFinder_Buffer implements InstantiatorFinderInterface {

  /**
   * @var \Donquixote\Annotation\InstantiatorFinder\InstantiatorFinderInterface
   */
  private $decorated;

  /**
   * @var \Donquixote\Annotation\Instantiator\InstantiatorInterface[]
   */
  private $buffer = [];

  /**
   * @param \Donquixote\Annotation\InstantiatorFinder\InstantiatorFinderInterface $decorated
   */
  public function __construct(InstantiatorFinderInterface $decorated) {
    $this->decorated = $decorated;
  }

  /**
   * @param string $class
   *
   * @return \Donquixote\Annotation\Instantiator\InstantiatorInterface
   */
  public function classGetInstantiator($class) {
    return array_key_exists($class, $this->buffer)
      ? $this->buffer[$class]
      : $this->buffer[$class] = $this->decorated->classGetInstantiator($class);
  }
}
