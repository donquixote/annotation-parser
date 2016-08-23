<?php

namespace Donquixote\Annotation\Resolver;

use Donquixote\Annotation\RawAst\RawDoctrineAnnotationInterface;
use Donquixote\Annotation\Value\DoctrineAnnotation\DoctrineAnnotation;

class AnnotationResolver_PrimitiveResolver implements AnnotationResolverInterface {

  /**
   * @var \Donquixote\Annotation\Resolver\PrimitiveResolverInterface
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
   * @param \Donquixote\Annotation\Resolver\PrimitiveResolverInterface $primitiveResolver
   */
  public function __construct(PrimitiveResolverInterface $primitiveResolver) {
    $this->primitiveResolver = $primitiveResolver;
  }

  /**
   * @param \Donquixote\Annotation\RawAst\RawDoctrineAnnotationInterface $rawAnnotation
   *
   * @return \Donquixote\Annotation\Value\DoctrineAnnotation\DoctrineAnnotationInterface
   *
   * @throws \Donquixote\Annotation\Resolver\ResolverException
   */
  public function resolveAnnotation(RawDoctrineAnnotationInterface $rawAnnotation) {

    if ([] !== $arguments = $rawAnnotation->getArguments()) {
      $arguments = $this->resolveArguments($arguments);
    }

    return new DoctrineAnnotation($rawAnnotation->getName(), $arguments);
  }

  /**
   * @param array $arguments
   *
   * @return array
   *
   * @throws \Donquixote\Annotation\Resolver\ResolverException
   */
  private function resolveArguments(array $arguments) {

    foreach ($arguments as $k => &$v) {
      if (is_string($v)) {
        $v = $this->primitiveResolver->resolvePrimitive($v);
      }
      elseif (is_array($v)) {
        $v = $this->resolveArguments($v);
      }
      elseif ($v instanceof RawDoctrineAnnotationInterface) {
        $v = $this->resolveAnnotation($v);
      }
      else {
        throw new ResolverException("Unexpected value !s", $v);
      }
    }

    return $arguments;
  }
}
