<?php

namespace Donquixote\Annotation;

use Donquixote\Annotation\Parser\AnnotationParser;
use Donquixote\Annotation\RawAst\BrokenDocTag;
use Donquixote\Annotation\RawAst\BrokenDoctrineAnnotation;
use Donquixote\Annotation\RawAst\RawDoctrineAnnotation;
use Donquixote\Annotation\RawAst\RawDoctrineAnnotationInterface;
use Donquixote\Annotation\RawAst\RawPhpDocAnnotation;
use Donquixote\Annotation\Resolver\AnnotationResolver_PrimitiveResolver;
use Donquixote\Annotation\Resolver\AnnotationResolverInterface;
use Donquixote\Annotation\Value\DoctrineAnnotation\DoctrineAnnotationInterface;

class DocCommentUtil {

  /**
   * @param string $docComment
   * @param string|null $tagName
   * @param \Donquixote\Annotation\Resolver\AnnotationResolverInterface|null $annotationResolver
   *
   * @return \Donquixote\Annotation\Value\DoctrineAnnotation\DoctrineAnnotationInterface[]
   */
  public static function docGetDoctrineAnnotations($docComment, $tagName = null, AnnotationResolverInterface $annotationResolver = null) {

    if (null === $annotationResolver) {
      $annotationResolver = AnnotationResolver_PrimitiveResolver::create();
    }

    $annotations = [];
    foreach (self::docGetRawDoctrineAnnotations($docComment, $tagName) as $rawAnnotation) {
      $annotations[] = $annotationResolver->resolveAnnotation($rawAnnotation);
    }

    return $annotations;
  }

  /**
   * @param string $docComment
   * @param string|null $tagName
   *
   * @return \Donquixote\Annotation\RawAst\RawDoctrineAnnotationInterface[]
   */
  public static function docGetRawDoctrineAnnotations($docComment, $tagName = null) {

    $annotations = [];
    foreach (self::docGetRawPieces($docComment) as $piece) {
      if ($piece instanceof RawDoctrineAnnotationInterface) {
        if (null === $tagName || $tagName === $piece->getName()) {
          $annotations[] = $piece;
        }
      }
    }

    return $annotations;
  }

  /**
   * @param string $docComment
   * @param string|null $tagName
   *
   * @return \Donquixote\Annotation\RawAst\RawPhpDocAnnotation[]
   */
  public static function docGetRawTags($docComment, $tagName = null) {

    $tags = [];
    foreach (self::docGetRawPieces($docComment) as $piece) {
      if ($piece instanceof RawPhpDocAnnotation) {
        if (null === $tagName || $tagName === $piece->getName()) {
          $tags[] = $piece;
        }
      }
    }

    return $tags;
  }

  /**
   * @param string $docComment
   *
   * @return mixed[]
   */
  public static function docGetRawPieces($docComment) {
    $cleanComment = self::docGetClean($docComment);
    return self::textGetRawPieces($cleanComment);
  }

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

  /**
   * @param string $cleanComment
   *
   * @return mixed[]
   */
  public static function textGetRawPieces($cleanComment) {

    if ('' === $cleanComment) {
      return [];
    }

    $cleanComment = trim($cleanComment);

    $splitComment = preg_split('~(?:^|\s*\v)@([a-zA-Z_][a-zA-Z_0-9]*)(\h*)~', $cleanComment, -1, PREG_SPLIT_DELIM_CAPTURE);
    $n = count($splitComment);

    $result = [];

    if ('' !== $splitComment[0]) {
      $result[] = $splitComment[0];
    }

    for ($i = 3; $i < $n; $i += 3) {

      $name = $splitComment[$i - 2];
      $space = $splitComment[$i - 1];
      $text = $splitComment[$i];

      if ('(' === $text[0]) {
        if (false === $arguments = self::parseDoctrineAnnotationBody($text)) {
          $result[] = new BrokenDoctrineAnnotation($name, $text);
        }
        else {
          $result[] = new RawDoctrineAnnotation($name, $arguments);
        }
      }
      else {
        if ('' !== $space) {
          $result[] = new RawPhpDocAnnotation($name, $text);
        }
        else {
          $result[] = new BrokenDocTag($name, $text);
        }
      }
    }

    return $result;
  }

  /**
   * @param string $snippet
   *
   * @return mixed[]|false
   */
  public static function parseDoctrineAnnotationBody($snippet) {
    $pos = 0;
    $parser = new AnnotationParser($snippet);
    return $parser->body($pos);
  }

}
