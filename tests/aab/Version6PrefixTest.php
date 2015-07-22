<?php
/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/22/15
 * Time: 1:11 PM
 */

namespace IPAddress\Validator;


use DI\ContainerBuilder;

class Version6PrefixTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \IPAddress\Validator\Version6\Version6Prefix
     */
    protected static $class;

    protected function setUp()
    {
        $container = ContainerBuilder::buildDevContainer();
        self::$class = $container->get('\IPAddress\Validator\Version6\Version6Prefix');
    }

    public function testSimplePrefix()
    {
        self::$class->isValid(64);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Prefix string is empty
     */
    public function testExceptionEmptyString()
    {
        self::$class->isValid('');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Prefix string is not numeric
     */
    public function testExceptionNotNumeric()
    {
        self::$class->isValid('sixtyfour');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Prefix string is not a valid range
     */
    public function testExceptionRangeTooLow()
    {
        // NOTE: testing for 0 would actually throw a value "string is empty" message
        self::$class->isValid(-1);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Prefix string is not a valid range
     */
    public function testExceptionRangeTooHight()
    {
        self::$class->isValid(129);
    }
}
