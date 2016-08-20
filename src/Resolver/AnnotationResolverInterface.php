<?php

namespace Donquixote\Annotation\Resolver;

use Donquixote\Annotation\RawAnnotation\RawDoctrineAnnotationInterface;

interface AnnotationResolverInterface {

  /**
   * @param \Donquixote\Annotation\RawAnnotation\RawDoctrineAnnotationInterface $rawAnnotation
   *
   * @return \Donquixote\Annotation\Value\DoctrineAnnotation\DoctrineAnnotationInterface
   */
  public function resolveAnnotation(RawDoctrineAnnotationInterface $rawAnnotation);

}
