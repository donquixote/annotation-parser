<?php

namespace Donquixote\Annotation\Resolver\Primitive;

interface PrimitiveResolverInterface {

  /**
   * @param string $s
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public function resolvePrimitive($s, \Reflector $reflector);

}
