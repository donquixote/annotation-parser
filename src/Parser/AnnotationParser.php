<?php

namespace Donquixote\Annotation\Parser;

use Donquixote\Annotation\DocCommentUtil;
use Donquixote\Annotation\RawAst\BrokenDoctrineAnnotation;
use Donquixote\Annotation\RawAst\RawDoctrineAnnotation;
use Donquixote\Annotation\RawAst\RawPhpDocAnnotation;
use Donquixote\Annotation\Value\Identifier\Identifier_ClassAliasConstant;
use Donquixote\Annotation\Value\Identifier\Identifier_ClassConstant;
use Donquixote\Annotation\Value\Identifier\Identifier_Fqcn;
use Donquixote\Annotation\Value\Identifier\Identifier_QcnOrAlias;

class AnnotationParser {

  /**
   * String with '@' appended as terminator character.
   *
   * @var string
   */
  private $text;

  /**
   * @param string $docComment
   *
   * @return self
   */
  public static function createFromDocComment($docComment) {
    return new self(DocCommentUtil::docGetClean($docComment));
  }

  /**
   * @param string $text
   */
  public function __construct($text) {
    if (!is_string($text)) {
      $type = gettype($text);
      throw new \InvalidArgumentException("First argument expected to be string, $type found instead.");
    }
    $this->text = $text . '@';
  }

  //                                                                   Structure
  // ---------------------------------------------------------------------------

  /**
   * @param int $i
   *
   * @return mixed[]
   */
  public function docElements($i) {

    $pattern0 = '~(\v|\G)@([a-zA-Z_][a-zA-Z0-9_]*)((\s*\(\s*(\)|))|\s+)()~';
    $pattern1 = '~(\v)@([a-zA-Z_][a-zA-Z0-9_]*)((\s*\(\s*(\)|))|\s+)()~';

    $elements = [];

    if (!preg_match($pattern0, $this->text, $m, PREG_OFFSET_CAPTURE, $i)) {
      // Wrap up the rest of the doc comment.
      $text = substr($this->text, $i, -1);
      if ('' !== $text) {
        $elements[] = $text;
      }
      return $elements;
    }

    $brokenDoctrineName = null;
    $nonDoctrineName = null;

    while (true) {

      if ('' !== $m[1][0]) {
        // Wrap up the part between $i and the current match.
        $text = substr($this->text, $i, $m[0][1] - $i);
        if (null !== $brokenDoctrineName) {
          $elements[] = new BrokenDoctrineAnnotation($brokenDoctrineName, $text);
        }
        elseif (null !== $nonDoctrineName) {
          $elements[] = new RawPhpDocAnnotation($nonDoctrineName, $text);
        }
        elseif ('' !== $text) {
          $elements[] = $text;
        }
      }

      $brokenDoctrineName = null;
      $nonDoctrineName = null;

      $name = $m[2][0];

      if ('' === $m[4][0]) {
        // Regular annotation aka '@Foo ..'.
        if ($i >= $m[3][1]) {
          throw new \RuntimeException("Infinite loop.");
        }
        $i = $m[3][1];
        $nonDoctrineName = $name;
      }
      elseif ('' !== $m[5][0]) {
        // Doctrine annotation aka '@Foo()', with empty argument list.
        $elements[] = new RawDoctrineAnnotation($name, []);
        if ($i >= $m[6][1]) {
          throw new \RuntimeException("Infinite loop.");
        }
        $i = $m[6][1];
      }
      else {
        // Doctrine annotation aka '@Foo(..)'.
        $j = $m[6][1];
        if (1
          && false !== ($arguments = $this->argumentsNonEmpty($j))
          && ')' === $this->text[$j]
        ) {
          $elements[] = new RawDoctrineAnnotation($name, $arguments);
          if ($i >= $j + 1) {
            throw new \RuntimeException("Infinite loop: $i > $j.");
          }
          $i = $j + 1;
        }
        else {
          // Broken doctrine annotation.
          if ($i >= $m[3][1]) {
            throw new \RuntimeException("Infinite loop.");
          }
          $i = $m[3][1];
          $brokenDoctrineName = $name;
        }
      }

      if (!preg_match($pattern1, $this->text, $m, PREG_OFFSET_CAPTURE, $i)) {
        break;
      }
    }

    // Wrap up the rest of the doc comment.
    $text = substr($this->text, $i, -1);
    if (null !== $brokenDoctrineName) {
      $elements[] = new BrokenDoctrineAnnotation($brokenDoctrineName, $text);
    }
    elseif (null !== $nonDoctrineName) {
      $elements[] = new RawPhpDocAnnotation($nonDoctrineName, $text);
    }
    elseif ('' !== $text) {
      $elements[] = $text;
    }

    return $elements;
  }

  /**
   * @param int $i
   *
   * @return \Donquixote\Annotation\RawAst\RawDoctrineAnnotation|false
   */
  public function doctrineAnnotation(&$i) {
    $j = $i;

    if (false === $name = $this->regex($j, '~\G@([a-zA-Z_][a-zA-Z0-9_]*)~', 1)) {
      return false;
    }

    $this->ws0($j);

    if (false === $arguments = $this->body($j)) {
      return false;
    }

    $i = $j;

    return new RawDoctrineAnnotation($name, $arguments);
  }

  /**
   * @param int $i
   *   before: $string[$i] === '('.
   *   after (success): $string[$i - 1] === ')'.
   *   after (failure): original position.
   *
   * @return mixed[]|false
   */
  public function body(&$i) {

    if ('(' !== $this->text[$i]) {
      return false;
    }

    $j = $i + 1;

    $this->ws0($j);

    if (')' === $this->text[$j]) {
      $i = $j + 1;
      return [];
    }

    if (false === $args = $this->argumentsNonEmpty($j)) {
      return false;
    }

    if (')' !== $this->text[$j]) {
      return false;
    }

    $i = $j + 1;

    return $args;
  }

  /**
   * @param int $i
   *
   * @return array|false
   *
   * @see body()
   */
  public function curlyArray(&$i) {

    if ('{' !== $this->text[$i]) {
      return false;
    }

    $j = $i + 1;

    $this->ws0($j);

    if ('}' === $this->text[$j]) {
      $i = $j + 1;
      return [];
    }

    if (false === $args = $this->argumentsNonEmpty($j)) {
      return false;
    }

    if ('}' !== $this->text[$j]) {
      return false;
    }

    $i = $j + 1;

    return $args;
  }

  /**
   * @param int $i
   *
   * @return mixed[]|false
   */
  public function argumentsNonEmpty(&$i) {
    $j = $i;

    $args = [];
    while (true) {
      if (false !== $identifier = $this->identifier($j)) {
        $this->ws0($j);
        if ('=' !== $this->text[$j]) {
          return false;
        }
        ++$j;
        $this->ws0($j);
      }

      if (false === $value = $this->value($j)) {
        return false;
      }

      if (false !== $identifier) {
        $args[$identifier] = $value;
      }
      else {
        $args[] = $value;
      }

      $this->ws0($j);

      if (',' !== $this->text[$j]) {
        break;
      }

      ++$j;

      $this->ws0($j);
    }

    $i = $j;
    return $args;
  }

  //                                                                       Value
  // ---------------------------------------------------------------------------

  /**
   * @param int $i
   *
   * @return mixed|false
   */
  public function value(&$i) {

    $c = $this->text[$i];

    if ('"' === $c) {
      return $this->regex($i, '~\G"([^\n\r\f"\\\\]+|\\\\[bnrf"\\\\])*"~');
    }

    if ('{' === $c) {
      return $this->curlyArray($i);
    }

    if ('@' === $c) {
      return $this->doctrineAnnotation($i);
    }

    return $this->regex($i, '~\G[^"{}@\(\),]*[^"{}@\(\),\s]~');
  }

  //                                                                 Identifiers
  // ---------------------------------------------------------------------------

  /**
   * @param int $i
   *
   * @return \Donquixote\Annotation\Value\Identifier\IdentifierInterface|false
   */
  public function constant(&$i) {
    if ('\\' === $this->text[$i]) {
      return $this->constantStartingWithNsSeparator($i);
    }
    else {
      return $this->constantStartingWithoutNsSeparator($i);
    }
  }

  /**
   * @param int $i
   *
   * @return \Donquixote\Annotation\Value\Identifier\IdentifierInterface|false
   */
  private function constantStartingWithNsSeparator(&$i) {

    if (1 !== preg_match('~\G((\\\\[a-zA-Z_][a-zA-Z0-9_]*)+)(::([a-zA-Z_][a-zA-Z0-9_]*)|)~', $this->text, $m, 0, $i)) {
      return false;
    }

    $i += strlen($m[0]);

    if (isset($m[4]) && '' !== $m[4]) {
      return Identifier_ClassConstant::createFromFqcn($m[1], $m[4]);
    }
    else {
      return Identifier_Fqcn::createFromFqcn($m[1]);
    }
  }

  /**
   * @param int $i
   *
   * @return \Donquixote\Annotation\Value\Identifier\IdentifierInterface|false
   */
  private function constantStartingWithoutNsSeparator(&$i) {

    if (1 !== preg_match('~\G([a-zA-Z_][a-zA-Z0-9_]*(\\\\[a-zA-Z_][a-zA-Z0-9_]*)*)(::([a-zA-Z_][a-zA-Z0-9_]*)|)~', $this->text, $m, 0, $i)) {
      return false;
    }

    $i += strlen($m[0]);

    if (isset($m[4]) && '' !== $m[4]) {
      return new Identifier_ClassAliasConstant($m[1], $m[4]);
    }
    else {
      return new Identifier_QcnOrAlias($m[1]);
    }
  }

  /**
   * @param int $i
   *
   * @return string|false
   */
  public function identifier(&$i) {
    return $this->regex($i, '~\G[a-zA-Z_][a-zA-Z0-9_]*~');
  }

  //                                                         Primitives: Strings
  // ---------------------------------------------------------------------------

  /**
   * @param int $i
   *
   * @return string|false
   */
  public function string(&$i) {

    if (1 !== preg_match('~\G"([^\n\r\f"\\\\]+|\\\\[bnrf"\\\\])*"~', $this->text, $m, 0, $i)) {
      return false;
    }

    $str = json_decode($m[0]);

    if (!is_string($str)) {
      return false;
    }

    $i += strlen($m[0]);

    return $str;
  }

  //                                                         Primitives: Numbers
  // ---------------------------------------------------------------------------

  /**
   * @param int $i
   *
   * @return float|int|false
   */
  public function number(&$i) {

    $c = $this->text[$i];

    if ('-' === $c) {
      return $this->numberStartingWithMinus($i);
    }

    if ('.' === $c) {
      return $this->numberStartingWithDot($i);
    }

    if ('0' === $c) {
      return $this->numberStartingWithZero($i);
    }

    if (ctype_digit($c)) {
      return $this->numberStartingWithNonZeroDigit($i);
    }

    return false;
  }

  /**
   * @param int $i
   *
   * @return float|int|false
   */
  private function numberStartingWithMinus(&$i) {

    ++$i;

    $c = $this->text[$i];

    if ('.' === $c) {
      $v = $this->numberStartingWithDot($i);
    }
    elseif ('0' === $c) {
      $v = $this->numberStartingWithZero($i);
    }
    elseif (ctype_digit($c)) {
      $v = $this->numberStartingWithNonZeroDigit($i);
    }
    else {
      $v = false;
    }

    if (false !== $v) {
      return -$v;
    }

    --$i;
    return false;
  }

  /**
   * @param int $i
   *
   * @return float|int|false
   */
  private function numberStartingWithDot(&$i) {

    if (1 !== preg_match('~\G\.\d+([eE][+-]?\d*|)~', $this->text, $m, 0, $i)) {
      return false;
    }

    $i += strlen($m[0]);

    return (float)$m[0];
  }

  /**
   * @param int $i
   *
   * @return float|int|false
   */
  private function numberStartingWithZero(&$i) {

    if (1 === preg_match('~\G0\d*((\.\d*|)[eE]{1}[+-]?\d+|\.\d*)~', $this->text, $m, 0, $i)) {
      $i += strlen($m[0]);
      return (float)$m[0];
    }

    $c = $this->text[$i + 1];

    if ('x' === $c || 'X' === $c) {
      // Hex.
      return $this->hexNotNegative($i);
    }

    if (!ctype_digit($c)) {
      // Number is '0' followed by something else.
      ++$i;
      return 0;
    }

    if (false !== $v = $this->octNotNegative($i)) {
      return $v;
    }

    return $this->decNotNegative($i);
  }

  /**
   * @param int $i
   *
   * @return float|int|false
   */
  private function numberStartingWithNonZeroDigit(&$i) {

    if (1 !== preg_match('~\G\d+((\.\d*|)[eE][+-]?\d+|\.\d*|)~', $this->text, $m, 0, $i)) {
      return false;
    }

    $i += strlen($m[0]);

    if ('' !== $m[1]) {
      return (float)$m[0];
    }
    else {
      return (int)$m[0];
    }
  }

  /**
   * @param int $i
   *
   * @return int|false
   */
  public function decNotNegative(&$i) {

    if (1 !== preg_match('~\G\d+~', $this->text, $m, 0, $i)) {
      return false;
    }

    $i += strlen($m[0]);

    return (int)$m[0];
  }

  /**
   * @param int $i
   *
   * @return int|false
   */
  public function dec(&$i) {

    if (1 !== preg_match('~\G-?\d+~', $this->text, $m, 0, $i)) {
      return false;
    }

    $i += strlen($m[0]);

    return (int)$m[0];
  }

  /**
   * @param int $i
   *
   * @return int|false
   */
  public function hexNotNegative(&$i) {

    if (1 !== preg_match('~\G0[xX]([A-Fa-f0-9]+)~', $this->text, $m, 0, $i)) {
      return false;
    }

    $i += strlen($m[0]);

    return hexdec($m[1]);
  }

  /**
   * @param int $i
   *
   * @return int|false
   */
  public function octNotNegative(&$i) {

    if (1 !== preg_match('~\G0(\d+)~', $this->text, $m, 0, $i)) {
      return false;
    }

    $i += strlen($m[0]);

    return octdec($m[1]);
  }

  //                                                                  Whitespace
  // ---------------------------------------------------------------------------

  /**
   * Optional whitespace.
   *
   * @param int $i
   *
   * @return string
   */
  public function ws0(&$i) {

    if (1 !== preg_match('~\G\s*~ms', $this->text, $m, 0, $i)) {
      throw new \RuntimeException("Optional whitespace parser is not supposed to fail.");
    }

    $i += strlen($m[0]);

    return $m[0];
  }

  /**
   * Required whitespace, including asterisks from comment linebreaks.
   *
   * @param int $i
   *
   * @return string|false
   */
  public function ws1(&$i) {

    if (1 !== preg_match('~\G\s+~ms', $this->text, $m, 0, $i)) {
      return false;
    }

    $i += strlen($m[0]);

    return $m[0];
  }

  //                                                                     Helpers
  // ---------------------------------------------------------------------------

  /**
   * @param int $i
   * @param string $pattern
   * @param int $k
   *
   * @return false|string
   */
  public function regex(&$i, $pattern, $k = 0) {

    if (1 !== preg_match($pattern, $this->text, $m, 0, $i)) {
      return false;
    }

    $i += strlen($m[0]);

    return $m[$k];
  }

  /**
   * @param int $i
   * @param string $pattern
   * @param array|null $m
   * @param int $flags
   *
   * @return int
   */
  public function regexMatch(&$i, $pattern, array &$m = null, $flags = 0) {
    return preg_match($pattern, $this->text, $m, $flags, $i);
  }

  /**
   * @param int $i
   * @param string $pattern
   * @param int $k
   *
   * @return bool|string
   */
  public function untilRegex(&$i, $pattern, $k = 0) {

    if (!preg_match($pattern, $this->text, $m, PREG_OFFSET_CAPTURE, $i)) {
      return false;
    }

    return substr($this->text, $i, $i = $m[$k][1]);
  }

  /**
   * @param int $i0
   * @param int $i1
   *
   * @return string
   */
  public function substr($i0, $i1) {
    return substr($this->text, $i0, $i1 - $i0);
  }

}
