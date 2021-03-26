<?php

declare(strict_types=1);

namespace IServ\Bridge\Zeit\Tests;

use IServ\Bridge\Zeit\TestClass;
use PHPUnit\Framework\TestCase;

/**
 * @covers \IServ\Bridge\Zeit\TestClass
 */
final class TestClassTest extends TestCase
{
    public function testTest(): void
    {
        $class = new TestClass();
        $this->assertTrue($class->returnsTrue());
    }
}
