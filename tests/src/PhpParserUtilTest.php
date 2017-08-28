<?php

namespace Donquixote\Annotation\Tests;

use Donquixote\Annotation\Tests\Fixtures\ClassWithVariousImports;
use Donquixote\Annotation\Util\PhpParserUtil;
use Donquixote\Annotation\Instantiator\InstantiatorInterface;
use Donquixote\Annotation\Reflector\CustomReflector;
use Donquixote\Annotation\Resolver\AnnotationResolver;
use Donquixote\Annotation\Util\DocCommentUtil;

class PhpParserUtilTest extends TestBase {

  public function testFileGetImports() {

    $class = ClassWithVariousImports::class;
    $reflectionClass = new \ReflectionClass($class);
    $file = $reflectionClass->getFileName();
    $imports = PhpParserUtil::fileGetImports($file);

    $this->assertSame(
      [
        'InstantiatorInterface' => InstantiatorInterface::class,
        'CustomReflector' => CustomReflector::class,
        'AnnotationResolver' => AnnotationResolver::class,
        'DocCommentUtil' => DocCommentUtil::class,
      ],
      $imports);
  }

  public function testFileGetHeadTokens() {

    $class = ClassWithVariousImports::class;
    $reflectionClass = new \ReflectionClass($class);
    $file = $reflectionClass->getFileName();

    $headTokens = PhpParserUtil::fileGetHeadTokens(
      $file,
      [T_CLASS]);

    $headPhp = '';
    foreach ($headTokens as $token) {
      if (isset($token[1])) {
        $headPhp .= $token[1];
      }
      else {
        $headPhp .= $token;
      }
    }

    $headPhpExpected = <<<'EOT'
<?php

namespace Donquixote\Annotation\Tests\Fixtures;

use Donquixote\Annotation\Instantiator\InstantiatorInterface;
use Donquixote\Annotation\Reflector\CustomReflector;
use Donquixote\Annotation\Resolver\AnnotationResolver;
use Donquixote\Annotation\Util\DocCommentUtil;


EOT;

    $this->assertSame($headPhpExpected, $headPhp);
  }

}
