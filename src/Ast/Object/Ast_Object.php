<?php

namespace Donquixote\Annotation\Ast\Object;

class Ast_Object implements Ast_ObjectInterface {

  /**
   * @var string
   */
  private $name;

  /**
   * @var array
   */
  private $arguments;

  /**
   * @param string $name
   * @param array $arguments
   */
  public function __construct($name, array $arguments) {
    $this->name = $name;
    $this->arguments = $arguments;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return mixed[]
   */
  public function getArguments() {
    return $this->arguments;
  }

}
