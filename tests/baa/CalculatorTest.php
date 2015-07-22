<?php

/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/22/15
 * Time: 5:53 PM
 */
class CalculatorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \IPAddress\Calculator
     */
    protected static $class;

    protected function setUp()
    {
        self::$class = \DI\ContainerBuilder::buildDevContainer()->get('\IPAddress\Calculator');
    }

    public function testResetIsFluid()
    {
        $actual = self::$class->reset();
        $this->assertInstanceOf('\IPAddress\Calculator', $actual);
    }

    /**
     * @dataProvider rangeProvider
     */
    public function testNetworkRanges($query, $expected)
    {
        $condition = self::$class->calc($query);

        $this->assertTrue($condition);
        $arr = self::$class->getNetworkRange();

        $this->assertArrayHasKey('min', $arr);
        $this->assertArrayHasKey('max', $arr);
        foreach ($expected as $key => $value ) {
            $this->assertEquals($value, $arr[$key]);
        }
    }

    public function testIsValidatedReturnsFalse() {
        $condition = self::$class->isValidated(false);
        $this->assertFalse($condition);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No validated values are available
     */
    public function testIsValidatedThrows() {
        self::$class->isValidated();
    }

    public function testInvalidQuery() {
        $condition = self::$class->calc('');
        $this->assertFalse($condition);
    }

    public function testGetErrorMessage() {
        self::$class->calc('');
        $messages = self::$class->getErrorMessages();

        $this->assertTrue(is_array($messages));
        $this->assertCount(1, $messages);

        $this->assertStringEndsWith('empty', $messages[0]);
    }

    public function rangeProvider()
    {
        $ranges = [
            ['abcd::5678:1234/103',
             ['min'   => 'abcd:0000:0000:0000:0000:0000:5600:0000',
              'max'   => 'abcd:0000:0000:0000:0000:0000:57ff:ffff',
              'hosts' => 33554432]],
            ['abcd:1234:fedc:1234:5678:1234:0:0/87',
             ['min'   => 'abcd:1234:fedc:1234:5678:1200:0000:0000',
              'max'   => 'abcd:1234:fedc:1234:5678:13ff:ffff:ffff',
              'hosts' => 2199023255552]],
            ['abcd:1234:fedc:1234:5678:1234:0:0/126',
             ['min'   => 'abcd:1234:fedc:1234:5678:1234:0000:0000',
              'max'   => 'abcd:1234:fedc:1234:5678:1234:0000:0003',
              'hosts' => 4]],
        ];

        return $ranges;
    }
}
