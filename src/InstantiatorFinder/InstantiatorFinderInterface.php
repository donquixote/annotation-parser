<?php

namespace Donquixote\Annotation\InstantiatorFinder;

interface InstantiatorFinderInterface {

  /**
   * @param string $class
   *
   * @return \Donquixote\Annotation\Instantiator\InstantiatorInterface|null
   */
  public function classGetInstantiator($class);

}
