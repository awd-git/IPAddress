<?php
/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/21/15
 * Time: 5:43 PM
 */

namespace IPAddress\Validator\Version6;


use DI\ContainerBuilder;

class Version6AddressValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \IPAddress\Validator\Version6\Version6Address
     */
    protected static $class;

    protected function setUp()
    {
        $container = ContainerBuilder::buildDevContainer();
        self::$class = $container->get('\IPAddress\Validator\Version6\Version6Address');
    }


    public function testRealShort()
    {
        $segments = ['74DC', '0000', '0000', '0000', '0000', '0000', '0000', '02BA'];
        $string = "{$segments[0]}::{$segments[7]}";

        $condition = self::$class->isValid($string);
        $this->assertTrue($condition);

        $actual = self::$class->getFormattedAddress();
        $expected = implode(':', $segments);
        $this->assertEquals($expected, $actual);
    }

    public function testSomewhatShort()
    {
        $segments = ['74DC', '0000', '0000', '0000', '0000', '0000', '0000', '02BA'];
        $string = str_replace('0000', '0', implode(':', $segments));

        $condition = self::$class->isValid($string);
        $this->assertTrue($condition);

        $expected = implode(':', $segments);
        $actual = self::$class->getFormattedAddress();
        $this->assertEquals($expected, $actual);
    }

    public function testMediumShort()
    {
        $segments = ['74DC', '0123', '0456', '0abc', '0000', '0000', '0000', '02BA'];
        $string = preg_replace('/:0/', ':', implode(':', $segments));

        $condition = self::$class->isValid($string);
        $this->assertTrue($condition);

        $expected = implode(':', $segments);
        $actual = self::$class->getFormattedAddress();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage IP address string is empty
     */
    public function testExceptionEmptyString()
    {
        self::$class->isValid('');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage IP address is not a string
     */
    public function testExceptionNotString()
    {
        self::$class->isValid(true);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage IP address string is too long
     */
    public function testExceptionStringTooLong()
    {
        $segment = 'abcd';
        $segments = [];
        for ($i = 0; $i < 9; $i++) {
            $segments[] = $segment;
        }
        self::$class->isValid(implode(':',$segments));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No validated IP address exists
     */
    public function testExceptionGetStringBeforeValidating()
    {
        self::$class->getFormattedAddress();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage IP address has too many segments
     */
    public function testExceptionTooManySegments()
    {
        $segments = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $string = implode(':', $segments);
        self::$class->isValid($string);
    }
}
