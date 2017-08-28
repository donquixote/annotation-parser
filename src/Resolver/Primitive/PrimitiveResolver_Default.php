<?php

namespace Donquixote\Annotation\Resolver\Primitive;

use Donquixote\Annotation\Exception\ResolverException;
use Donquixote\Annotation\Value\Identifier\Identifier_ClassAliasConstant;
use Donquixote\Annotation\Value\Identifier\Identifier_ClassConstant;
use Donquixote\Annotation\Value\Identifier\Identifier_Fqcn;
use Donquixote\Annotation\Value\Identifier\Identifier_QcnOrAlias;

class PrimitiveResolver_Default implements PrimitiveResolverInterface {

  /**
   * @var mixed[]
   */
  private $map;

  /**
   * @param mixed[] $map
   *
   * @return self
   */
  public static function create(array $map = []) {

    return new self(
      $map + [
        'true' => true,
        'TRUE' => true,
        'false' => false,
        'FALSE' => false,
        'null' => null,
        'NULL' => null,
      ]);
  }

  /**
   * @param mixed[] $map
   */
  public function __construct(array $map = []) {
    $this->map = $map + array_combine($range = range(-9, 9), $range);
  }

  /**
   * @param string $s
   * @param \Reflector $reflector
   *
   * @return mixed
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  public function resolvePrimitive($s, \Reflector $reflector) {

    if (array_key_exists($s, $this->map)) {
      return $this->map[$s];
    }

    if ('' === $s) {
      throw new ResolverException("Primitive is empty string.");
    }

    $c = $s[0];

    if ($c === $s) {
      // Only one character.
      if (preg_match('~[a-zA-Z_]~', $s)) {
        return new Identifier_QcnOrAlias($s);
      }
      throw new ResolverException("Invalid one-char primitive: !s.", $s);
    }

    if ('"' === $c) {
      if (!is_string($v = json_decode($s))) {
        throw new ResolverException("Not a string literal: ($s).");
      }
      return $v;
    }

    if ('.' === $c) {
      // Float starting with '.'.
      if (!preg_match('~^\.\d+([eE][+-]?\d*|)$~', $s, $m)) {
        throw new ResolverException("Not a float: !s.", $s);
      }

      return (float)$m[0];
    }

    if ('-' === $c) {
      return $this->numberStartingWithMinus($s);
    }

    if ('0' === $c) {
      return $this->numberStartingWithZero($s);
    }

    if (ctype_digit($c)) {

      if (!preg_match('~^\d+((\.\d*|)[eE][+-]?\d+|\.\d*|)$~', $s, $m)) {
        throw new ResolverException("Not a number: !s.", $s);
      }

      return '' !== $m[1]
        ? (float) $m[0]
        : (int) $m[0];
    }

    if ('\\' === $c) {
      return $this->constantStartingWithNsSeparator($s);
    }

    return $this->constantStartingWithoutNsSeparator($s);
  }

  //                                                                 Identifiers
  // ---------------------------------------------------------------------------

  /**
   * @param string $s
   *
   * @return \Donquixote\Annotation\Value\Identifier\IdentifierInterface
   *
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  private function constantStartingWithNsSeparator($s) {

    if (!preg_match('~^((\\\\[a-zA-Z_][a-zA-Z0-9_]*)+)(::([a-zA-Z_][a-zA-Z0-9_]*)|)$~', $s, $m)) {
      throw new ResolverException("Not an FQCN: !s.", $s);
    }

    return isset($m[4]) && '' !== $m[4]
      ? Identifier_ClassConstant::createFromFqcn($m[1], $m[4])
      : Identifier_Fqcn::createFromFqcn($m[1]);
  }

  /**
   * @param string $s
   *
   * @return \Donquixote\Annotation\Value\Identifier\IdentifierInterface
   *
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  private function constantStartingWithoutNsSeparator($s) {

    if (!preg_match('~^([a-zA-Z_][a-zA-Z0-9_]*(\\\\[a-zA-Z_][a-zA-Z0-9_]*)*)(::([a-zA-Z_][a-zA-Z0-9_]*)|)$~', $s, $m)) {
      throw new ResolverException("Not a QCN or alias: !s.", $s);
    }

    return isset($m[4]) && '' !== $m[4]
      ? new Identifier_ClassAliasConstant($m[1], $m[4])
      : new Identifier_QcnOrAlias($m[1]);
  }

  //                                                                     Numbers
  // ---------------------------------------------------------------------------

  /**
   * @param string $s
   *
   * @return float|int
   *
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  private function numberStartingWithMinus($s) {

    $c = $s[1];

    if ('.' === $c) {
      if (!preg_match('~^\-\.\d+([eE][+-]?\d*|)$~', $s, $m)) {
        throw new ResolverException("Not a float: !s.", $s);
      }
      return (float)$m[0];
    }

    if ('0' === $c) {
      return -$this->numberStartingWithZero(substr($s, 1));
    }

    if (!preg_match('~^\-\d+((\.\d*|)[eE][+-]?\d+|\.\d*|)$~', $s, $m)) {
      throw new ResolverException("Not a number: !s.", $s);
    }

    return '' !== $m[1]
      ? (float) $m[0]
      : (int) $m[0];
  }

  /**
   * @param string $s
   *
   * @return float|int
   *
   * @throws \Donquixote\Annotation\Exception\ResolverException
   */
  private function numberStartingWithZero($s) {

    if (preg_match('~^0\d*((\.\d*|)[eE]{1}[+-]?\d+|\.\d*)$~', $s, $m)) {
      return (float)$m[0];
    }

    $c = $s[1];

    if ('x' === $c || 'X' === $c) {
      // Hex.
      if (!preg_match('~^0[xX]([A-Fa-f0-9]+)$~', $s, $m)) {
        throw new ResolverException("Not a hex number: !s.", $s);
      }
      return hexdec($m[1]);
    }

    if (preg_match('~^0(\d+)$~', $s, $m)) {
      return octdec($m[1]);
    }

    throw new ResolverException("Not a number: !s.", $s);
  }

}
