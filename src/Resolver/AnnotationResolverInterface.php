<?php

namespace Donquixote\Annotation\Resolver;

use Donquixote\Annotation\Ast\Object\Ast_ObjectInterface;

interface AnnotationResolverInterface {

  /**
   * @param \Donquixote\Annotation\Ast\Object\Ast_ObjectInterface $ast
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public function resolveAnnotation(Ast_ObjectInterface $ast, \Reflector $reflector);

}
