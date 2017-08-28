<?php

namespace Donquixote\Annotation\Util;

class ReflectionUtil {

  /**
   * @param \Reflector $reflector
   *
   * @return null|string
   */
  public static function reflectorGetCacheId(\Reflector $reflector) {

    if ($reflector instanceof \ReflectionClass) {
      return $reflector->getName();
    }

    if ($reflector instanceof \ReflectionProperty) {
      return $reflector->getDeclaringClass()->getName()
        . '::$'
        . $reflector->getName();
    }

    if ($reflector instanceof \ReflectionMethod) {
      return $reflector->getDeclaringClass()->getName()
        . '::'
        . $reflector->getName()
        . '()';
    }

    return NULL;
  }

  /**
   * @param \Reflector $reflector
   *
   * @return null|string
   */
  public static function reflectorGetFile(\Reflector $reflector) {

    if (method_exists($reflector, 'getFileName')) {
      return $reflector->getFileName();
    }

    if (NULL !== $class = self::reflectorGetdeclaringClassOrTrait($reflector)) {
      return $class->getFileName();
    }

    return NULL;
  }

  /**
   * @param \Reflector $reflector
   *
   * @return \ReflectionClass|null
   */
  public static function reflectorGetdeclaringClassOrTrait(\Reflector $reflector) {

    if ($reflector instanceof \ReflectionClass) {
      return $reflector;
    }

    if ($reflector instanceof \ReflectionProperty) {
      return self::propertyGetDeclaringClassOrTrait($reflector);
    }

    if ($reflector instanceof \ReflectionMethod) {
      return self::methodGetDeclaringClassOrTrait($reflector);
    }

    return NULL;
  }

  /**
   * @param \ReflectionMethod $method
   *
   * @return \ReflectionClass
   */
  public static function methodGetDeclaringClassOrTrait(\ReflectionMethod $method) {

    $original = self::methodGetOriginal($method);

    return $original->getDeclaringClass();
  }

  /**
   * @param \ReflectionMethod $method
   *
   * @return \ReflectionMethod
   */
  public static function methodGetOriginal(\ReflectionMethod $method) {

    $declaringClass = $method->getDeclaringClass();

    if (self::classOwnsMethod($declaringClass, $method)) {
      return $method;
    }

    $traits = $declaringClass->getTraits();
    $traitAliases = $declaringClass->getTraitAliases();

    if (isset($traitAliases[$method->getName()])) {

      list($traitName, $traitMethodName) = explode(
        '::',
        $traitAliases[$method->getName()]);

      if (isset($traits[$traitName])) {
        $trait = $traits[$traitName];
        if ($trait->hasMethod($traitMethodName)) {
          $traitMethod = $trait->getMethod($traitMethodName);
          if (self::methodsAreIdentical($traitMethod, $method)) {
            return self::methodGetOriginal($traitMethod);
          }
        }
      }

      return NULL;
    }

    foreach ($declaringClass->getTraits() as $traitName => $trait) {

      if ($trait->hasMethod($method->getName())) {
        $traitMethod = $trait->getMethod($method->getName());

        if (self::methodsAreIdentical($traitMethod, $method)) {
          return self::methodGetOriginal($traitMethod);
        }
      }
    }

    throw new \RuntimeException(
      "Declaring class or trait not found. This is not expected to happen.");
  }

  /**
   * @param \ReflectionMethod $a
   * @param \reflectionMethod $b
   *
   * @return bool
   */
  private static function methodsAreIdentical(\ReflectionMethod $a, \reflectionMethod $b) {

    return true
      && $a->getFileName() === $b->getFileName()
      && $a->getStartLine() === $b->getStartLine()
      && $a->getEndLine() === $b->getEndLine()
      && $a->getDocComment() === $b->getDocComment();
  }

  /**
   * @param \ReflectionClass $class
   * @param \ReflectionMethod $method
   *
   * @return bool
   */
  private static function classOwnsMethod(\ReflectionClass $class, \ReflectionMethod $method) {

    return true
      && $method->getFileName() === $class->getFileName()
      && $method->getStartLine() >= $class->getStartLine()
      && $method->getEndLine() <= $class->getEndLine();
  }

  /**
   * @param \ReflectionProperty $property
   *
   * @return \ReflectionClass
   */
  public static function propertyGetDeclaringClassOrTrait(\ReflectionProperty $property) {

    $original = self::propertyGetOriginal($property);

    return $original->getDeclaringClass();
  }

  /**
   * @param \ReflectionProperty $property
   *
   * @return \ReflectionProperty
   */
  public static function propertyGetOriginal(\ReflectionProperty $property) {

    $propertyName = $property->getName();
    $declaringClass = $property->getDeclaringClass();

    foreach ($declaringClass->getTraits() as $trait) {

      if (!$trait->hasProperty($propertyName)) {
        continue;
      }

      $traitProperty = $trait->getProperty($propertyName);

      if ($traitProperty->getDocComment() !== $property->getDocComment()) {
        continue;
      }

      return self::propertyGetOriginal($traitProperty);
    }

    return $property;
  }

}
