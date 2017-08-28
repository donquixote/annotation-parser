<?php

namespace Donquixote\Annotation\Tests;

use/** @noinspection PhpUndefinedClassInspection */
  /** @noinspection PhpUndefinedNamespaceInspection */
  PHPUnit\Framework\TestCase;

// Travis will use a different version of PhpUnit depending on the PHP version.

/** @noinspection PhpUndefinedClassInspection */
if (class_exists(TestCase::class)) {
  /** @noinspection PhpUndefinedClassInspection */
  class_alias(TestCase::class, TestBase::class);
}
elseif (class_exists(\PHPUnit_Framework_TestCase::class)) {
  class_alias(\PHPUnit_Framework_TestCase::class, TestBase::class);
}
else {
  throw new \RuntimeException("No test suite was found.");
}
