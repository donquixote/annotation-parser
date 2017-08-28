<?php

namespace Donquixote\Annotation\ArgumentsMapper;

class ArgumentsMapper_Doctrine implements ArgumentsMapperInterface {

  /**
   * @param array $args
   *
   * @return array
   */
  public function argsGetArgs(array $args) {

    if (isset($args[0])) {
      $args['value'] = $args[0];
      unset($args[0]);
    }

    return [$args];
  }

}
