<?php

namespace Donquixote\Annotation\ContextFinder;

interface ContextFinderInterface {

  /**
   * @param \Reflector $reflector
   *
   * @return \Donquixote\Annotation\Context\ContextInterface
   */
  public function reflectorGetContext(\Reflector $reflector);

}
