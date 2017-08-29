<?php

namespace Donquixote\Annotation\Tests;

use Donquixote\Annotation\Ast\Object\Ast_Object;
use Donquixote\Annotation\Ast\PhpDoc\Ast_PhpDoc;
use Donquixote\Annotation\Util\DocCommentUtil;

class DocCommentUtilTest extends TestBase {

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
    self::assertSameExport($expected, DocCommentUtil::textGetAst($text), $text);
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
          new Ast_Object('Foo', []),
          new Ast_PhpDoc('param', 'float $length'),
          new Ast_PhpDoc('param', 'string $name
  The name.'),
          new Ast_PhpDoc('return', 'float')
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
