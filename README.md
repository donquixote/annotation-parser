[![Build Status](https://secure.travis-ci.org/donquixote/annotation-parser.png)](https://travis-ci.org/donquixote/annotation-parser)

# Donquixote's Annotation Parser

Alternative to other annotation parsers. Specifically [doctrine/annotations](https://github.com/doctrine/annotations).


## Usage

Look into [AnnotationReaderTest](tests/src/AnnotationReaderTest.php) for inspiration.

The non-trivial part is to create the [AnnotationReader](src/Reader/AnnotationReader.php), more specifically the [AnnotationResolver](src/Resolver/AnnotationResolver.php), from smaller components, depending on the desired behavior.


## Difference to doctrine/annotations

The main difference is that annotation parsing in this library happens in two steps:

1. Parsing the doc comment into an abstract syntax tree (AST).
2. Resolving this AST into annotation objects or any other kind of structure.

This separations allows to remove some assumptions of how annotations work, and make them optional.

E.g. in Doctrine, the idea is that an annotation tag name refers to a class name or class alias, and parsing the annotation means instantiating this class. Here, this behavior is just one possibility among others.

In Doctrine, the way an annotation class is instantiated depends on metadata on the annotation class. Here, such behavior could be implemented as an InstantiatorFinder, but it is not baked into the main architecture.

It is interesting to know that Doctrine is currently planning a 2.x branch, see https://github.com/doctrine/annotations/pull/75. There is also an interesting issue at https://github.com/doctrine/annotations/issues/139.

## Stability

For the 1.0 branch I might still do some renames, which are guaranteed to break user code. So treat it as preview quality!

However, any other projects that I maintain that depend on this library will be ok.

The 0.0 branch is kinda stable, as far as BC breaks are concerned. 

## Use case

For now, I created this to use it in my own projects, such as https://drupal.org/project/cfr. At the time I started this, doctrine/annotations did not feel like the right tool.

If others find it useful, let me know!


