<?php
/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/21/15
 * Time: 5:43 PM
 */

namespace IPAddress\Filter;


use DI\ContainerBuilder;

class Version6AddressFilterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \IPAddress\Filter\Version6\Version6Address
     */
    protected static $class;

    protected function setUp() {
        $container = ContainerBuilder::buildDevContainer();
        self::$class = $container->get('\IPAddress\Filter\Version6\Version6Address');
    }

    public function testRealShort()
    {
        $segments = ['74DC', '0000', '0000', '0000', '0000', '0000', '0000', '02BA'];
        $string = "{$segments[0]}::{$segments[7]}";

        $actual = self::$class->filter($string);
        $expected = implode(':', $segments);
        $this->assertEquals($expected, $actual);
    }

    public function testSomewhatShort()
    {
        $segments = ['74DC', '0000', '0000', '0000', '0000', '0000', '0000', '02BA'];
        $string = str_replace('0000', '0', implode(':', $segments));

        $actual = self::$class->filter($string);
        $expected = implode(':', $segments);
        $this->assertEquals($expected, $actual);
    }

    public function testMediumShort()
    {
        $segments = ['74DC', '0123', '0456', '0abc', '0000', '0000', '0000', '02BA'];
        $string = preg_replace('/:0/', ':', implode(':', $segments));

        $actual = self::$class->filter($string);
        $expected = implode(':', $segments);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage IP address string is empty
     */
    public function testExceptionEmptyString()
    {
        self::$class->filter('');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage IP address has too many segments
     */
    public function testExceptionTooManySegments()
    {
        $segments = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $string = implode(':', $segments);
        self::$class->filter($string);
    }
}
