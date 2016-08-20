<?php

namespace Donquixote\Annotation\Resolver;

interface PrimitiveResolverInterface {

  /**
   * @param string $s
   *
   * @return mixed
   *
   * @throws ResolverException
   */
  public function resolvePrimitive($s);

}
