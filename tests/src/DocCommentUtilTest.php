<?php

namespace Donquixote\Annotation\Tests;

use Donquixote\Annotation\DocCommentUtil;
use Donquixote\Annotation\RawAst\RawDoctrineAnnotation;
use Donquixote\Annotation\RawAst\RawPhpDocAnnotation;

class DocCommentUtilTest extends \PHPUnit_Framework_TestCase {

  public function testDocGetClean() {

    foreach ([

      '/**
   * Computes the average.
   *
   * @param float $x
   * @param float $y
   *
   * @return float
   */' => '
Computes the average.

@param float $x
@param float $y

@return float',

      '/**
   * Is unfair.
   *
   */' => '
Is unfair.
',
      '/** Really? */' => ' Really?',
    ] as $docComment => $expected) {
      self::assertSame($expected, DocCommentUtil::docGetClean($docComment), $docComment);
    }

    foreach ([
      '/**
   *Is unfair.
   */',
      '/**
   Is unfair.
   */',
      '/*
   * Is unfair.
   */',
      '/**Is unfair.*/',
      '/**/',
      '/**',
      '*/',
      '',
      '/**Meh */',
      '/** Meh*/',
      '/**Meh*/',
    ] as $docComment) {
      self::assertNull(DocCommentUtil::docGetClean($docComment), $docComment);
    }
  }

  /**
   * @param string $text
   * @param mixed[] $expected
   *
   * @dataProvider providerTextGetRawPieces
   */
  public function testTextGetRawPieces($text, array $expected) {
    self::assertSameExport($expected, DocCommentUtil::textGetRawPieces($text), $text);
  }

  /**
   * @return array[]
   *   Format: $[] = [$text, $expected]
   */
  public function providerTextGetRawPieces() {
    return [
      ['', []],
      [
        'It flows like a river.

@Foo()

@param float $length
@param string $name
  The name.

@return float
',
        [
          'It flows like a river.',
          new RawDoctrineAnnotation('Foo', []),
          new RawPhpDocAnnotation('param', 'float $length'),
          new RawPhpDocAnnotation('param', 'string $name
  The name.'),
          new RawPhpDocAnnotation('return', 'float')
        ]
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
    self::assertSame(var_export($expected, true), var_export($actual, true), $message);
  }

}
