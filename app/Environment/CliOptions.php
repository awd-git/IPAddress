<?php
/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/17/15
 * Time: 1:21 PM
 */

namespace IPAddress\Environment;


class CliOptions
{

    public static $shortops = '';
    public static $longops = ['cidr:'];

    protected static $CIDR;
    protected $ops = [];

    public function __construct(array $ops)
    {
        $this->processOps($ops);
    }

    public function getValues()
    {
        return [
            'cidr'     => self::getCIDR(),
        ];
    }

    public static function getCIDR() {
        return (string) self::$CIDR;
    }

    protected function processOps(array $ops)
    {
        $this->ops = $ops;
        $this->filterCIDR();
    }

    protected function filterCIDR()
    {
        if (!isset($this->ops['cidr'])) {
            throw new \Exception('CIDR value is missing');
        } elseif (!strlen($this->ops['cidr']) === 0 ) {
            throw new \Exception('CIDR value is empty');
        }

        self::$CIDR = $this->ops['cidr'];
    }
}