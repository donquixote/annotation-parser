<?php

namespace Donquixote\Annotation\Instantiator;

use Donquixote\Annotation\ArgumentsMapper\ArgumentsMapperInterface;

class Instantiator_NewInstanceArgs implements InstantiatorInterface {

  /**
   * @var \ReflectionClass
   */
  private $reflectionClass;

  /**
   * @var \Donquixote\Annotation\ArgumentsMapper\ArgumentsMapperInterface
   */
  private $argumentsMapper;

  /**
   * @param \ReflectionClass $reflectionClass
   * @param \Donquixote\Annotation\ArgumentsMapper\ArgumentsMapperInterface $argumentsMapper
   */
  public function __construct(
    \ReflectionClass $reflectionClass,
    ArgumentsMapperInterface $argumentsMapper
  ) {
    $this->reflectionClass = $reflectionClass;
    $this->argumentsMapper = $argumentsMapper;
  }

  /**
   * @param mixed[] $args
   * @param \Reflector $reflector
   *
   * @return mixed
   */
  public function instantiate(array $args, \Reflector $reflector) {

    $args = $this->argumentsMapper->argsGetArgs($args);

    return $this->reflectionClass->newInstanceArgs($args);
  }
}
