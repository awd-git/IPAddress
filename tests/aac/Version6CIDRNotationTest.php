<?php

/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/22/15
 * Time: 1:11 PM
 */
namespace IPAddress\Validator\Version6;

use DI\ContainerBuilder;

class Version6CIDRNotationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Version6CIDRNotation
     */
    protected static $class;

    protected function setUp() {
        $container = ContainerBuilder::buildDevContainer();
        self::$class = $container->get('\IPAddress\Validator\Version6\Version6CIDRNotation');
    }

    public function testSimpleFullNotation() {
        $expected = '1234:abcd:1234:abcd:1234:abcd:1234:abcd/108';
        self::$class->isValid($expected);
        $address = self::$class->getAddress();
        $prefix = self::$class->getPrefix();

        $this->assertEquals($expected, "{$address}/{$prefix}");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Query string is empty
     */
    public function testExceptionQueryStringIsEmpty() {
        self::$class->isValid('');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Query string is too long
     */
    public function testExceptionQueryStringIsTooLong() {
        $expected = '12345:abcd:1234:1234:1234:abcd:1234:abcd/108';
        self::$class->isValid($expected);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage CIDR notation format is not valid
     */
    public function testExceptionMissingPrefix() {
        $expected = '1234:abcd:1234:abcd:1234:abcd:1234:abcd';
        self::$class->isValid($expected);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Prefix string is empty
     */
    public function testExceptionPrefixIsMissing() {
        $expected = '1234:abcd:1234:abcd:1234:abcd:1234:abcd/';
        self::$class->isValid($expected);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Prefix string is empty
     */
    public function testExceptionPrefixIsEmpty() {
        $expected = '1234:abcd:1234:abcd:1234:abcd:1234:abcd/0';
        self::$class->isValid($expected);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Prefix string is not a valid range
     */
    public function testExceptionPrefixIsOutOfRange() {
        $expected = '1234:abcd:1234:abcd:1234:abcd:1234:abcd/129';
        self::$class->isValid($expected);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No validated IP address exists
     */
    public function testExceptionGetAddressWithoutValidation() {
        self::$class->getAddress();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage No validated prefix exists
     */
    public function testExceptionGetPrefixWithoutValidation() {
        self::$class->getPrefix();
    }

}
