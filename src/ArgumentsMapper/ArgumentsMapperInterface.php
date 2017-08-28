<?php

namespace Donquixote\Annotation\ArgumentsMapper;

interface ArgumentsMapperInterface {

  /**
   * @param array $args
   *
   * @return array
   */
  public function argsGetArgs(array $args);
}
