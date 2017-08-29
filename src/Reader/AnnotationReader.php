<?php

namespace Donquixote\Annotation\Reader;

use Donquixote\Annotation\Ast\Object\Ast_ObjectInterface;
use Donquixote\Annotation\Resolver\AnnotationResolverInterface;
use Donquixote\Annotation\Util\DocCommentUtil;

class AnnotationReader {

  /**
   * @var \Donquixote\Annotation\Resolver\AnnotationResolverInterface
   */
  private $annotationResolver;

  /**
   * @param \Donquixote\Annotation\Resolver\AnnotationResolverInterface $annotationResolver
   */
  public function __construct(AnnotationResolverInterface $annotationResolver) {
    $this->annotationResolver = $annotationResolver;
  }

  /**
   * @param \Reflector $reflector
   *
   * @return mixed[]
   */
  public function reflectorGetAnnotations(\Reflector $reflector) {

    if (!method_exists($reflector, 'getDocComment')) {
      return [];
    }

    if (false === $docComment = $reflector->getDocComment()) {
      return [];
    }

    // @todo Make the parser injectable?
    $astNodes = DocCommentUtil::docGetAst($docComment);

    $annotations = [];
    foreach ($astNodes as $astNode) {
      if ($astNode instanceof Ast_ObjectInterface) {
        $annotations[] = $this->annotationResolver->resolveAnnotation(
          $astNode,
          $reflector);
      }
    }

    return $annotations;
  }

}
