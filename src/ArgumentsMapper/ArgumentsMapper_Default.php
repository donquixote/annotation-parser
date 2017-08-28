<?php

namespace Donquixote\Annotation\ArgumentsMapper;

class ArgumentsMapper_Default implements ArgumentsMapperInterface {

  /**
   * @param array $args
   *
   * @return array
   */
  public function argsGetArgs(array $args) {
    return [$args];
  }

}
