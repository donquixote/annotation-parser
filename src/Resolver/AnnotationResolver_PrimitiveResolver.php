<?php

namespace Donquixote\Annotation\Resolver;

use Donquixote\Annotation\Ast\Object\Ast_ObjectInterface;
use Donquixote\Annotation\Exception\ResolverException;
use Donquixote\Annotation\Resolver\Primitive\PrimitiveResolver_Default;
use Donquixote\Annotation\Resolver\Primitive\PrimitiveResolverInterface;
use Donquixote\Annotation\Value\GenericAnnotation\GenericAnnotation;

class AnnotationResolver_PrimitiveResolver implements AnnotationResolverInterface {

  /**
   * @var \Donquixote\Annotation\Resolver\Primitive\PrimitiveResolverInterface
   */
  private $primitiveResolver;

  /**
   * @param array $map
   *
   * @return self
   */
  public static function create(array $map = []) {
    return new self(PrimitiveResolver_Default::create($map));
  }

  /**
   * @param \Donquixote\Annotation\Resolver\Primitive\PrimitiveResolverInterface $primitiveResolver
   */
  public function __construct(PrimitiveResolverInterface $primitiveResolver) {
    $this->primitiveResolver = $primitiveResolver;
  }

  /**
   * @param \Donquixote\Annotation\Ast\Object\Ast_ObjectInterface $ast
   * @param \Reflector $reflector
   *
   * @return mixed
   * @internal param \Donquixote\Annotation\Context\ContextInterface $context
   */
  public function resolveAnnotation(Ast_ObjectInterface $ast, \Reflector $reflector) {

    if ([] !== $arguments = $ast->getArguments()) {
      $arguments = $this->resolveArguments($arguments, $reflector);
    }

    return new GenericAnnotation($ast->getName(), $arguments);
  }

  /**
   * @param mixed[] $arguments
   * @param \Reflector $reflector
   *
   * @return mixed[]
   *
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  private function resolveArguments(array $arguments, \Reflector $reflector) {

    foreach ($arguments as $k => &$v) {
      if (is_string($v)) {
        $v = $this->primitiveResolver->resolvePrimitive($v, $reflector);
      }
      elseif (is_array($v)) {
        $v = $this->resolveArguments($v, $reflector);
      }
      elseif ($v instanceof Ast_ObjectInterface) {
        $v = $this->resolveAnnotation($v,$reflector);
      }
      else {
        throw new ResolverException("Unexpected value !s", $v);
      }
    }

    return $arguments;
  }
}
