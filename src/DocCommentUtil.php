<?php

namespace Donquixote\Annotation;

class DocCommentUtil {

  /**
   * Removes the asterisks from a doc comment.
   *
   * @param string $docComment
   *
   * @return string|null
   *   The cleaned-up doc comment, or
   *   NULL, if $docComment does not have the expected format.
   */
  public static function docGetClean($docComment) {

    if (!preg_match('~^/\*\*(\s.*\S)?\s+\*/$~s', $docComment, $m)) {
      return null;
    }

    if (!isset($m[1]) || '' === $m[1]) {
      return '';
    }

    $substr = $m[1];

    if (preg_match('~\v\h*[^\*\h]~', $substr)) {
      // Found a line that does not begin like '  *'
      return null;
    }

    if (preg_match('~\v\h*\*[^\s$]~', $substr)) {
      // Found a line where the '  *' is not followed by a whitespace or EOF.
      return null;
    }

    return preg_replace('~\v\h*\*\h?~', "\n", $substr);
  }

}
