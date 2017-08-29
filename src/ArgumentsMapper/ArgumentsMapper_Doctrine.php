<?php

namespace Donquixote\Annotation\ArgumentsMapper;

/**
 * In doctrine, the first anonymous value of an array gets the key 'value'
 * assigned. E.g. the arguments for @Message("Hello") are ['value' => 'Hello'].
 */
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
