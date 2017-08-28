<?php

namespace Donquixote\Annotation\ContextFinder;

use Donquixote\Annotation\Context\Context;
use Donquixote\Annotation\Util\PhpParserUtil;
use Donquixote\Annotation\Util\ReflectionUtil;

class ContextFinder_PhpTokenParser implements ContextFinderInterface {

  /**
   * @param \Reflector $reflector
   *
   * @return \Donquixote\Annotation\Context\ContextInterface
   */
  public function reflectorGetContext(\Reflector $reflector) {

    $class = ReflectionUtil::reflectorGetdeclaringClassOrTrait($reflector);

    if (NULL === $class) {
      return new Context(NULL, []);
    }

    $namespace = $class->getNamespaceName();

    // @todo Inject the parser?
    $imports = PhpParserUtil::fileGetImports($class->getFileName());

    return new Context($namespace, $imports);
  }
}
