<?php

namespace Donquixote\Annotation\Instantiator;

use Donquixote\Annotation\Exception\ResolverException;

class Instantiator_ResolverException implements InstantiatorInterface {

  /**
   * @var string
   */
  private $message;

  /**
   * @param string $message
   */
  public function __construct($message) {
    $this->message = $message;
  }

  /**
   * @param mixed[] $args
   * @param \Reflector $reflector
   *
   * @return mixed
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  public function instantiate(array $args, \Reflector $reflector) {
    throw new ResolverException($this->message);
  }
}
