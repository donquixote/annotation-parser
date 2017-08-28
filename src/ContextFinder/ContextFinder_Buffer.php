<?php

namespace Donquixote\Annotation\ContextFinder;

use Donquixote\Annotation\Util\ReflectionUtil;

class ContextFinder_Buffer implements ContextFinderInterface {

  /**
   * @var \Donquixote\Annotation\ContextFinder\ContextFinderInterface
   */
  private $decorated;

  /**
   * @var \Donquixote\Annotation\Context\ContextInterface[]
   */
  private $buffer = [];

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

    if (NULL === $cid = ReflectionUtil::reflectorGetCacheId($reflector)) {
      return $this->decorated->reflectorGetContext($reflector);
    }

    return array_key_exists($cid, $this->buffer)
      ? $this->buffer[$cid]
      : $this->buffer[$cid] = $this->decorated->reflectorGetContext($reflector);
  }
}
