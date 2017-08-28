<?php

namespace Donquixote\Annotation\Ast\Object;

/**
 * Represents annotations like "@Something("hello", label = @t("Hello"),
 *   active = true)".
 *
 * @see \Donquixote\Annotation\Tests\AnnotationParserTest::
 */
interface Ast_ObjectInterface {

  /**
   * Gets the annotation tag name without the '@'.
   *
   * @return string
   *   E.g. "Something".
   */
  public function getName();

  /**
   * Gets the arguments of the "()" part of the annotations.
   *
   * Entries without an array key automatically get numeric keys.
   *
   * Each array value is one of:
   * - A RawDoctrineAnnotationInterface object,
   *   for values like "@Translate("some text")".
   * - A string, containing the exact trimmed entry as-is.
   *   This is for constants, string literals, and numbers.
   * - An array of the same structure,
   *   for values with curly brackets like "{a = "A", b = @B(..)}".
   *
   * @return mixed[]
   *   E.g.:
   *   [
   *     0 => '"hello"',
   *     'label' => new RawDoctrineAnnotation("t", [0 => "Hello"]),
   *     'active' => 'true',
   *   ].
   */
  public function getArguments();
}
