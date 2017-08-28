<?php

namespace Donquixote\Annotation\Util;

use Donquixote\Annotation\Parser\Php\TokenParser;

class PhpParserUtil {

  /**
   * @param string $file
   *
   * @return string[]
   */
  public static function fileGetImports($file) {

    $tokenizer = TokenParser::createFromFile($file);

    return $tokenizer->parseUseStatements();
  }

  /**
   * @param string $file
   * @param int[] $stoppingTokenNames
   *   Format: $[$tokenName] = TRUE
   *
   * @return mixed[]
   */
  public static function fileGetHeadTokens($file, array $stoppingTokenNames) {
    return self::readHeadTokens(
      self::fileGetLines($file),
      $stoppingTokenNames);
  }

  /**
   * @param \Traversable $lines
   * @param int[] $stoppingTokenNames
   *   Format: $[$tokenName] = TRUE
   *
   * @return mixed[]
   */
  public static function readHeadTokens(\Traversable $lines, array $stoppingTokenNames) {

    $stop = array_fill_keys($stoppingTokenNames, TRUE);

    $remaining = '';

    $headTokens = [];
    foreach ($lines as $line) {

      $php = $remaining . $line . '/**/';

      $lineTokens = token_get_all($php);

      if ('' !== $remaining) {
        $t = array_shift($lineTokens);

        if (NULL === $t) {
          throw new \RuntimeException("Expected T_OPEN_TAG, found nothing.");
        }

        if (T_OPEN_TAG !== $t[0]) {
          throw new \RuntimeException("Expected T_OPEN_TAG.");
        }
      }

      $lastToken = array_pop($lineTokens);

      if (!isset($lastToken[1])) {
        $remaining = '<?php ' . substr($lastToken, 0, -4);
      }
      elseif ($lastToken[1] === '/**/') {
        $remaining = '<?php ';
      }
      else {
        $remaining = '<?php ' . substr($lastToken[1], 0,-4);
      }

      foreach ($lineTokens as $token) {

        if (isset($stop[$token[0]])) {
          break 2;
        }

        $headTokens[] = $token;
      }
    }

    return $headTokens;
  }

  /**
   * @param $file
   *
   * @return \Generator
   */
  public static function fileGetLines($file) {

    $h = fopen($file, 'rb');

    while (false !== $line = fgets($h)) {
      yield $line;
    }
  }

}
