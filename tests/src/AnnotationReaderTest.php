<?php

namespace Donquixote\Annotation\Tests;

use Donquixote\Annotation\ContextFinder\ContextFinder_CustomReflectorDecorator;
use Donquixote\Annotation\ContextFinder\ContextFinder_PhpTokenParser;
use Donquixote\Annotation\InstantiatorFinder\InstantiatorFinder_Annotation;
use Donquixote\Annotation\Reader\AnnotationReader;
use Donquixote\Annotation\Resolver\AnnotationResolver;
use Donquixote\Annotation\Resolver\ClassName\ClassNameResolver_Default;
use Donquixote\Annotation\Resolver\Object\ObjectResolver_Instantiator;
use Donquixote\Annotation\Resolver\Primitive\PrimitiveResolver_Default;
use Donquixote\Annotation\Tests\Fixtures\Annotated\MyAnnotatedClass;
use Donquixote\Annotation\Tests\Fixtures\Annotation\Hello;

class AnnotationReaderTest extends \PHPUnit_Framework_TestCase {

  /**
   * @dataProvider providerReadAnnotations()
   *
   * @param \Reflector $reflector
   * @param array $expected
   */
  public function testReadAnnotations(\Reflector $reflector, array $expected) {

    $reader = $this->getReader();

    $this->assertEquals(
      $expected,
      $reader->reflectorGetAnnotations($reflector));
  }

  /**
   * @return array[]
   *   Format: $[$label] = [$reflector, $expected]
   */
  public function providerReadAnnotations() {

    $class = MyAnnotatedClass::class;

    $argss = [];

    $argss['class'] = [
      $r = new \ReflectionClass($class),
      [
        new Hello(['First class annotation.'], $r),
        new Hello(['Second class annotation.'], $r),
      ]
    ];

    $argss['property'] = [
      $r = new \ReflectionProperty($class, 'x'),
      [
        new Hello(['Annotated property.'], $r),
      ]
    ];

    $argss['method'] = [
      $r = new \ReflectionMethod($class, 'foo'),
      [
        new Hello(['This is method foo().'], $r),
      ]
    ];

    return $argss;
  }

  /**
   * @return \Donquixote\Annotation\Reader\AnnotationReader
   */
  private function getReader() {

    $primitiveResolver = PrimitiveResolver_Default::create();

    $contextFinder = new ContextFinder_PhpTokenParser();
    $contextFinder = new ContextFinder_CustomReflectorDecorator(
      $contextFinder);

    $classNameResolver = new ClassNameResolver_Default(
      $contextFinder);

    $instantiatorFinder = new InstantiatorFinder_Annotation();

    $objectResolver = new ObjectResolver_Instantiator(
      $classNameResolver,
      $instantiatorFinder);

    $annotationResolver = new AnnotationResolver(
      $primitiveResolver,
      $objectResolver);

    $reader = new AnnotationReader(
      $annotationResolver);

    return $reader;
  }

}
