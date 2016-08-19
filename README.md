[![Build Status](https://secure.travis-ci.org/donquixote/annotation-parser.png)](https://travis-ci.org/donquixote/annotation-parser)

# Donquixote's Annotation Parser

Alternative to other annotation parsers. Specifically the one from Doctrine.


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
$position = 0;
if (false !== $position = strpos('@Foo', $docText)) {
  $annotation = $parser->doctrineAnnotation($position);
}
```
