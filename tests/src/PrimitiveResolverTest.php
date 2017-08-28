<?php

namespace Donquixote\Annotation\Tests;

use Donquixote\Annotation\Reflector\CustomReflector;
use Donquixote\Annotation\Resolver\Primitive\PrimitiveResolver_Default;
use Donquixote\Annotation\Value\Identifier\Identifier_ClassAliasConstant;
use Donquixote\Annotation\Value\Identifier\Identifier_ClassConstant;
use Donquixote\Annotation\Value\Identifier\Identifier_Fqcn;
use Donquixote\Annotation\Value\Identifier\Identifier_QcnOrAlias;

class PrimitiveResolverTest extends TestBase {

  public function testResolvePrimitive() {

    $reflector = new CustomReflector();

    $resolver = PrimitiveResolver_Default::create(
      [
        'MY_CONST' => '(value of MY_CONST)',
        '#asdf c' => '(exotic mapping)',
      ]);

    foreach ([

      // PHP constants.
      'null' => null,
      'TRUE' => true,

      // String literals.
      '"xyz"' => 'xyz',

      // Mapped values.
      'MY_CONST' => '(value of MY_CONST)',
      '#asdf c' => '(exotic mapping)',

      // Identifiers.
      '\Nn' => Identifier_Fqcn::createFromFqcn('\Nn'),
      '\Nn\Cc' => Identifier_Fqcn::createFromFqcn('\Nn\Cc'),
      'Nn' => new Identifier_QcnOrAlias('Nn'),
      'Nn\Cc' => new Identifier_QcnOrAlias('Nn\Cc'),

      // Class constants.
      '\Nn::Xx' => Identifier_ClassConstant::createFromFqcn('\Nn', 'Xx'),
      '\Nn\Cc::Xx' => Identifier_ClassConstant::createFromFqcn('\Nn\Cc', 'Xx'),
      'Nn::Xx' => new Identifier_ClassAliasConstant('Nn', 'Xx'),
      'Nn\Cc::Xx' => new Identifier_ClassAliasConstant('Nn\Cc', 'Xx'),

    ] as $s => $expected) {
      $s = (string)$s;
      self::assertSameExport(
        $expected,
        $resolver->resolvePrimitive($s, $reflector));
    }

    // Numbers.
    // Use space padding to prevent PHP from turning these array indices into
    // numbers.
    foreach ([

      // int
      ' 0' => 0,
      ' 2' => 2,
      ' 123456' => 123456,
      ' 88' => 88,

      // float
      ' 07.' => 7.,
      ' 7.5' => 7.5,
      ' .558' => 0.558,

      // sci float
      ' 7e3' => 7e3,
      ' 6E+24' => 6e24,
      ' .5E+3' => .5e3,
      ' 4.e-3' => 4.e-3,
      ' 1e0' => 1e0,
      ' 0e0' => 0e0,

      // hex
      ' 0x11' => 0x11,
      ' 0xfff' => 0xfff,
      ' 0XFFF' => 0xfff,

      // oct
      ' 000' => 000,
      ' 01' => 01,
      ' 077' => 077,
      ' 010' => 010,

    ] as $s => $expected) {
      $s = substr($s, 1);
      self::assertSameExport(
        $expected,
        $resolver->resolvePrimitive($s, $reflector));
    }
  }

  /**
   * @param string $s
   *
   * @dataProvider providerResolvePrimitiveFail
   *
   * @expectedException \Donquixote\Annotation\Exception\ResolverException
   */
  public function testResolvePrimitiveFail($s) {
    $reflector = new CustomReflector();
    $resolver = PrimitiveResolver_Default::create();
    $resolver->resolvePrimitive($s, $reflector);
  }

  /**
   * @return array[]
   *   Format: $[] = [$s]
   */
  public function providerResolvePrimitiveFail() {

    $datasets = [];
    foreach ([
      '',
      '"',
      '#',
      '-x',
      '0m',
    ] as $s) {
      $datasets[] = [$s];
    }

    return $datasets;
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
