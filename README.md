[![Build Status](https://secure.travis-ci.org/donquixote/annotation-parser.png)](https://travis-ci.org/donquixote/annotation-parser)

# Donquixote's Annotation Parser

Alternative to other annotation parsers. Specifically [doctrine/annotations](https://github.com/doctrine/annotations).


```php
use Donquixote\Annotation\Parser\AnnotationParser;

$docText = '
  @Foo(
    x = "7",
    y = @Bar()
  )

  @return bool
';
$parser = new AnnotationParser($docText);
if (false !== $position = strpos('@Foo', $docText)) {
  $annotation = $parser->doctrineAnnotation($position);
}
```

## Difference to the doctrine/annotations

There are two main differences:
- Annotations are parsed into an abstract syntax tree (AST), before resolving identifiers and constants.
- Unlike in Doctrine, there is no such a concept like "annotation classes". An annotation name is just a string, it is not interpreted a class alias.

This being said, I imagine it not too hard to implement Doctrine's "annotation class" concept on top of this library.

## Use case

For now, I created this to use it in my own projects, such as https://drupal.org/project/cfr.
For this project I did not need "annotation classes". I might change my mind in the future.

If others find it useful, let me know!


