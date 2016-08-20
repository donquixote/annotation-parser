<?php

namespace Donquixote\Annotation\Tests;

use Donquixote\Annotation\DocCommentUtil;

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

}
