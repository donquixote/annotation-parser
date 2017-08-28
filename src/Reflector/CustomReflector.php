<?php

namespace Donquixote\Annotation\Reflector;

class CustomReflector extends CustomReflectorBase {

  /**
   * This is the least useful method in \Reflector.
   *
   * Also, it has a weird signature.
   *
   * @return string
   */
  public static function export() {
    throw new \RuntimeException("Not supported.");
  }
}
