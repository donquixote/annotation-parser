<?php

namespace Donquixote\Annotation\Tests;

use Donquixote\Annotation\Parser\AnnotationParser;
use Donquixote\Annotation\RawAst\BrokenDoctrineAnnotation;
use Donquixote\Annotation\RawAst\RawDoctrineAnnotation;
use Donquixote\Annotation\RawAst\RawPhpDocAnnotation;
use Donquixote\Annotation\Value\Identifier\Identifier_ClassConstant;
use Donquixote\Annotation\Value\Identifier\Identifier_QcnOrAlias;

class AnnotationParserTest extends \PHPUnit_Framework_TestCase {

  //                                                                   Structure
  // ---------------------------------------------------------------------------

  public function testDocElements() {

    foreach ([
      '' => [],
      'Gets the beer.

@foo()

@m()@x()
@bar(
  x = 6
)
@z()

@param int $n
  How many beers.
@param float|null $amount

@return bool
' => [
        "Gets the beer.\n",
        new RawDoctrineAnnotation('foo', []),
        "\n",
        new RawDoctrineAnnotation('m', []),
        "@x()",
        new RawDoctrineAnnotation('bar', ['x' => '6']),
        new RawDoctrineAnnotation('z', []),
        "\n",
        new RawPhpDocAnnotation('param', " int \$n\n  How many beers."),
        new RawPhpDocAnnotation('param', " float|null \$amount\n"),
        new RawPhpDocAnnotation('return', " bool\n"),
      ],
      '
@missingSpace#
@missingClosingBracket(
@missingOpeningBracket)' => [
        "\n@missingSpace#",
        new BrokenDoctrineAnnotation('missingClosingBracket', "(\n@missingOpeningBracket)"),
      ],
    ] as $text => $expected) {
      self::assertSameExport($expected, p($text)->docElements(0), $text);
    }
  }

  public function testDoctrineAnnotation() {

    foreach ([

      '@Foo(x = "7", y = {})' => new RawDoctrineAnnotation('Foo', [
        'x' => '"7"',
        'y' => [],
      ]),

      '@Foo(x = "7", y = @Bar())' => new RawDoctrineAnnotation('Foo', [
        'x' => '"7"',
        'y' => new RawDoctrineAnnotation('Bar', []),
      ]),

      '@Foo(
  x
  =
"7"
  ,
  y = @Bar()
)' => new RawDoctrineAnnotation('Foo', [
        'x' => '"7"',
        'y' => new RawDoctrineAnnotation('Bar', []),
      ]),

    ] as $text => $expected) {
      $i = 0;
      self::assertSameExport($expected, p($text)->doctrineAnnotation($i), $text);
      self::assertSame(strlen($text), $i, $text);
    }

    foreach ([
      '@Foo',
      '@Foo(',
      '@Foo(@Bar(',
    ] as $text) {
      $i = 0;
      self::assertFalse(p($text)->doctrineAnnotation($i), $text);
      self::assertSame(0, $i, $text);
    }
  }

  public function testBody() {

    $i = 0;
    self::assertSame(
      ['x' => '"7"', 'y' => []],
      p('(x = "7", y = {})')->body($i));
    self::assertSame(17, $i);

    $i = 0;
    self::assertSame(
      [],
      p('()')->body($i));
    self::assertSame(2, $i);
  }

  public function testCurlyArray() {

    $i = 0;
    self::assertSameExport(
      ['x' => '"7"', 'y' => 'TRUE'],
      p('{x = "7", y = TRUE},')->curlyArray($i));
    self::assertSame(19, $i);
  }

  public function testArguments() {

    $i = 0;
    self::assertSameExport(
      ['x' => '"7"', 'y' => 'TRUE'],
      p('x = "7", y = TRUE')->argumentsNonEmpty($i));
    self::assertSame(17, $i);

    $i = 0;
    self::assertSameExport(
      ['x' => '"7"', 'y' => 'TRUE'],
      p('x = "7", y = TRUE}')->argumentsNonEmpty($i));
    self::assertSame(17, $i);
  }

  //                                                                       Value
  // ---------------------------------------------------------------------------

  public function testValue() {

    $i = 0;
    self::assertSameExport('TRUE', p('TRUE')->value($i));
    self::assertSame(4, $i);

    $i = 0;
    self::assertSame('"abc"', p('"abc"')->value($i));
    self::assertSame(5, $i);
  }

  //                                                                 Identifiers
  // ---------------------------------------------------------------------------

  public function testConstant() {

    $i = 0;
    self::assertSameExport(new Identifier_QcnOrAlias('TRUE'), p('TRUE,')->constant($i));
    self::assertSame(4, $i);

    $i = 0;
    self::assertSameExport(Identifier_ClassConstant::createFromFqcn('\No\Bu', 'XY'), p('\No\Bu::XY,')->constant($i));
    self::assertSame(10, $i);
  }

  public function testIdentifier() {

    $i = 0;
    self::assertSame('xYz', p('xYz,')->identifier($i));
    self::assertSame(3, $i);
  }

  //                                                         Primitives: Strings
  // ---------------------------------------------------------------------------

  public function testString() {

    $i = 0;
    self::assertSame('xYz', p('"xYz"')->string($i));
    self::assertSame(5, $i);

    $i = 0;
    self::assertFalse(p('"a')->string($i));
    self::assertSame(0, $i);
  }

  //                                                         Primitives: Numbers
  // ---------------------------------------------------------------------------

  public function testNumber() {

    $cases = [
      '0' => 0,
      '2' => 2,
      '123456' => 123456,
      '07.' => 7.,
      '7.5' => 7.5,
      '.558' => 0.558,
      '88' => 88,
      '7e3' => 7e3,
      '6E+3' => 6e3,
      '.5E+3' => .5e3,
      '4.e-3' => 4.e-3,
      '1e0' => 1e0,
      '0e0' => 0e0,
      '0x11' => 0x11,
      '0xfff' => 0xfff,
      '0XFFF' => 0xfff,
      '000' => 000,
      '01' => 01,
      '077' => 077,
      '010' => 010,
    ];

    foreach ($cases as $str => $n) {
      $cases['-' . $str] = -$n;
    }

    foreach ($cases as $str => $n) {
      $str = (string)$str;
      $msg = var_export($str, true);

      if (is_float($n)) {
        self::assertSame($n, (float)$str, "(float)$msg !== $n");
      }

      $i = 0;
      self::assertSame($n, p($str)->number($i), "$n !== p($msg)->number().");
      self::assertSame($len = strlen($str), $i, "After parsing $msg, \$i === $i.");
    }

    foreach ([''] as $str) {
      $str = (string)$str;
      $i = 0;
      self::assertFalse(p($str)->number($i), $str);
    }
  }

  //                                                                  Whitespace
  // ---------------------------------------------------------------------------

  public function testWs0() {

    $p = p("The horse (\t\n).");

    $i = 3;
    $p->ws0($i);
    self::assertSame(4, $i);

    $i = 11;
    $p->ws0($i);
    self::assertSame(13, $i);

    $i = 2;
    $p->ws0($i);
    self::assertSame(2, $i);

    $i = 0;
    p(' ')->ws0($i);
    self::assertSame(1, $i);
  }

  public function testWs1() {

    $p = p("The horse (\t\n).");

    $i = 3;
    self::assertSame(' ', $p->ws1($i));
    self::assertSame(4, $i);

    $i = 11;
    self::assertSame("\t\n", $p->ws1($i));
    self::assertSame(13, $i);

    $i = 2;
    self::assertFalse($p->ws1($i));
    self::assertSame(2, $i);
  }

  //                                                                       Regex
  // ---------------------------------------------------------------------------

  public function testRegex() {

    $i = 1;
    self::assertSame('Yz', p('xYz65,')->regex($i, '~\G[a-zA-Z]*~'));
    self::assertSame(3, $i);
  }

  public function testUntilRegex() {

    $i = 0;
    self::assertSame('xYza', p('xYzafsd')->untilRegex($i, '~za(fs)~', 1));
    self::assertSame(4, $i);
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

/**
 * @param string $text
 *
 * @return \Donquixote\Annotation\Parser\AnnotationParser
 */
function p($text) {
  return new AnnotationParser($text);
}

/**
 * @param string $name
 * @param array $arguments
 *
 * @return \Donquixote\Annotation\RawAst\RawDoctrineAnnotation
 */
function rda($name, array $arguments) {
  return new RawDoctrineAnnotation($name, $arguments);
}

/**
 * @param string $name
 * @param string $text
 *
 * @return \Donquixote\Annotation\RawAst\RawPhpDocAnnotation
 */
function rdt($name, $text) {
  return new RawPhpDocAnnotation($name, $text);
}
