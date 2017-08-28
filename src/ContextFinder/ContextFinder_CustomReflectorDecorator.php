<?php

namespace Donquixote\Annotation\ContextFinder;

use Donquixote\Annotation\Context\Context;
use Donquixote\Annotation\Reflector\CustomReflectorInterface;

class ContextFinder_CustomReflectorDecorator implements ContextFinderInterface {

  /**
   * @var \Donquixote\Annotation\ContextFinder\ContextFinderInterface
   */
  private $decorated;

  /**
   * @param \Donquixote\Annotation\ContextFinder\ContextFinderInterface $decorated
   */
  public function __construct(ContextFinderInterface $decorated) {
    $this->decorated = $decorated;
  }

  /**
   * @param \Reflector $reflector
   *
   * @return \Donquixote\Annotation\Context\ContextInterface
   */
  public function reflectorGetContext(\Reflector $reflector) {

    if ($reflector instanceof CustomReflectorInterface) {
      $namespace = $reflector->getNamespace();
      $imports = $reflector->getImports();
      return new Context($namespace, $imports);
    }

    return $this->decorated->reflectorGetContext($reflector);
  }
}
