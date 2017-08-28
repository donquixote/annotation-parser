<?php

namespace Donquixote\Annotation\Tests\Fixtures;

use Donquixote\Annotation\Instantiator\InstantiatorInterface;
use Donquixote\Annotation\Reflector\CustomReflector;
use Donquixote\Annotation\Resolver\AnnotationResolver;
use Donquixote\Annotation\Util\DocCommentUtil;

class ClassWithVariousImports {

  public function foo() {
    return [
      AnnotationResolver::class,
      DocCommentUtil::class,
      CustomReflector::class,
      InstantiatorInterface::class,
    ];
  }

}
