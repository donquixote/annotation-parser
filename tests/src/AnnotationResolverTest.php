<?php

namespace Donquixote\Annotation\Tests;

use Donquixote\Annotation\RawAnnotation\RawDoctrineAnnotation;
use Donquixote\Annotation\RawAnnotation\RawDoctrineAnnotationInterface;
use Donquixote\Annotation\Resolver\AnnotationResolver_PrimitiveResolver;
use Donquixote\Annotation\Value\DoctrineAnnotation\DoctrineAnnotation;
use Donquixote\Annotation\Value\DoctrineAnnotation\DoctrineAnnotationInterface;

class AnnotationResolverTest extends \PHPUnit_Framework_TestCase {

  /**
   * @param \Donquixote\Annotation\RawAnnotation\RawDoctrineAnnotationInterface $raw
   * @param \Donquixote\Annotation\Value\DoctrineAnnotation\DoctrineAnnotationInterface $expected
   *
   * @dataProvider providerResolveAnnotation
   */
  public function testResolveAnnotation(RawDoctrineAnnotationInterface $raw, DoctrineAnnotationInterface $expected) {
    $resolver = AnnotationResolver_PrimitiveResolver::create();
    self::assertSameExport($expected, $resolver->resolveAnnotation($raw));
  }

  /**
   * @return array[]
   */
  public function providerResolveAnnotation() {
    return [
      [
        new RawDoctrineAnnotation('Foo', [
          'x' => new RawDoctrineAnnotation('X', [
            'y' => '"abc"',
          ]),
        ]),
        new DoctrineAnnotation('Foo', [
          'x' => new DoctrineAnnotation('X', [
            'y' => 'abc',
          ]),
        ]),
      ],
      [
        new RawDoctrineAnnotation('Foo', [
          'TRUE',
          'FALSE',
          'NULL',
        ]),
        new DoctrineAnnotation('Foo', [
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
