<?php

namespace App\Tests\Utils;

use App\Util\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testAdd()
    {
        $calculator = new Calculator();

        $result = $calculator->add(30, 10);

        $this->assertEquals(40, $result);
    }
}
