<?php

namespace Donquixote\Annotation\Tests;

use Donquixote\Annotation\Ast\Object\Ast_Object;
use Donquixote\Annotation\Ast\Object\Ast_ObjectInterface;
use Donquixote\Annotation\Reflector\CustomReflector;
use Donquixote\Annotation\Resolver\AnnotationResolver_PrimitiveResolver;
use Donquixote\Annotation\Value\GenericAnnotation\GenericAnnotation;
use Donquixote\Annotation\Value\GenericAnnotation\GenericAnnotationInterface;

class AnnotationResolver2Test extends \PHPUnit_Framework_TestCase {

  /**
   * @param \Donquixote\Annotation\Ast\Object\Ast_ObjectInterface $raw
   * @param \Donquixote\Annotation\Value\GenericAnnotation\GenericAnnotationInterface $expected
   *
   * @dataProvider providerResolveAnnotation
   */
  public function testResolveAnnotation(Ast_ObjectInterface $raw, GenericAnnotationInterface $expected) {

    // The basic resolver does not care about the reflection class.
    $reflector = new CustomReflector();

    $resolver = AnnotationResolver_PrimitiveResolver::create();
    self::assertSameExport($expected, $resolver->resolveAnnotation($raw, $reflector));
  }

  /**
   * @return array[]
   */
  public function providerResolveAnnotation() {
    return [
      [
        new Ast_Object('Foo', [
          'x' => new Ast_Object('X', [
            'y' => '"abc"',
          ]),
        ]),
        new GenericAnnotation('Foo', [
          'x' => new GenericAnnotation('X', [
            'y' => 'abc',
          ]),
        ]),
      ],
      [
        new Ast_Object('Foo', [
          'TRUE',
          'FALSE',
          'NULL',
        ]),
        new GenericAnnotation('Foo', [
          true,
          false,
          null,
        ]),
      ],
    ];
  }

  //                                                          Test class helpers
  // ---------------------------------------------------------------------------

  /**
   * @param mixed $expected
   * @param mixed $actual
   * @param string $message
   */
  private static function assertSameExport($expected, $actual, $message = '') {
    self::assertEquals($expected, $actual, $message);
    self::assertSame(var_export($expected, true), var_export($actual, true), $message);
  }

}
