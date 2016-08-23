<?php

namespace Donquixote\Annotation\Resolver;

use Donquixote\Annotation\RawAst\RawDoctrineAnnotationInterface;

interface AnnotationResolverInterface {

  /**
   * @param \Donquixote\Annotation\RawAst\RawDoctrineAnnotationInterface $rawAnnotation
   *
   * @return \Donquixote\Annotation\Value\DoctrineAnnotation\DoctrineAnnotationInterface
   */
  public function resolveAnnotation(RawDoctrineAnnotationInterface $rawAnnotation);

}
