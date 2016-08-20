<?php

namespace Donquixote\Annotation\Resolver;

class ResolverException extends \Exception {

  /**
   * @param string $message
   * @param mixed $s
   */
  public function __construct($message, $s = null) {
    parent::__construct(str_replace('!s', var_export($s, true), $message));
  }

}
