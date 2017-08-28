<?php

namespace Donquixote\Annotation\Resolver;

use Donquixote\Annotation\Ast\Object\Ast_ObjectInterface;
use Donquixote\Annotation\Exception\ResolverException;
use Donquixote\Annotation\Resolver\Object\ObjectResolver_GenericAnnotationClass;
use Donquixote\Annotation\Resolver\Object\ObjectResolverInterface;
use Donquixote\Annotation\Resolver\Primitive\PrimitiveResolver_Default;
use Donquixote\Annotation\Resolver\Primitive\PrimitiveResolverInterface;

class AnnotationResolver implements AnnotationResolverInterface {

  /**
   * @var \Donquixote\Annotation\Resolver\Primitive\PrimitiveResolverInterface
   */
  private $primitiveResolver;

  /**
   * @var \Donquixote\Annotation\Resolver\Object\ObjectResolverInterface
   */
  private $objectResolver;

  /**
   * @return self
   */
  public static function createGeneric() {
    return new self(
      new PrimitiveResolver_Default(),
      new ObjectResolver_GenericAnnotationClass());
  }

  /**
   * @param \Donquixote\Annotation\Resolver\Primitive\PrimitiveResolverInterface $primitiveResolver
   * @param \Donquixote\Annotation\Resolver\Object\ObjectResolverInterface $objectResolver
   */
  public function __construct(
    PrimitiveResolverInterface $primitiveResolver,
    ObjectResolverInterface $objectResolver
  ) {
    $this->primitiveResolver = $primitiveResolver;
    $this->objectResolver = $objectResolver;
  }

  /**
   * @param \Donquixote\Annotation\Ast\Object\Ast_ObjectInterface $ast
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public function resolveAnnotation(Ast_ObjectInterface $ast, \Reflector $reflector) {

    $args = $this->resolveArguments(
      $ast->getArguments(),
      $reflector);

    return $this->objectResolver->resolve(
      $ast->getName(),
      $args,
      $reflector);
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

    $args = [];
    foreach ($arguments as $k => $v) {
      $args[$k] = $this->resolveValue($v, $reflector);
    }

    return $args;
  }

  /**
   * @param mixed $v
   * @param \Reflector $reflector
   *
   * @return mixed
   *
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  private function resolveValue($v, \Reflector $reflector) {

    switch (gettype($v)) {

      case 'string':
        return $this->primitiveResolver->resolvePrimitive($v, $reflector);

      case 'array':
        return $this->resolveArguments($v, $reflector);

      case 'object':
        if ($v instanceof Ast_ObjectInterface) {
          return $this->resolveAnnotation($v, $reflector);
        }
        throw new ResolverException("Unexpected value class !s", get_class($v));

      default:
        throw new ResolverException("Unexpected value !s", $v);
    }
  }
}
