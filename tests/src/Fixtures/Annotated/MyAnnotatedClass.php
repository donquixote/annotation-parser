<?php

namespace Donquixote\Annotation\Tests\Fixtures\Annotated;

use Donquixote\Annotation\Tests\Fixtures\Annotation\Hello;

/**
 * @Hello("First class annotation.")
 * @Hello("Second class annotation.")
 */
class MyAnnotatedClass {

  /**
   * @Hello("Annotated property.")
   *
   * @var string
   */
  public $x;

  /**
   * @Hello("This is method foo().")
   */
  public function foo() {}

}
