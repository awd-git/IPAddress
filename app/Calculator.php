<?php
namespace IPAddress;

use IPAddress\Validator\Version6\Version6CIDRNotation;

/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/2/15
 * Time: 1:38 PM
 */
class Calculator
{

    protected $errors = [];
    protected $segments = [];
    protected $address;
    protected $prefix;
    protected $network_range = [];
    protected $group;
    /**
     * @var Version6CIDRNotation
     */
    private $validator;

    public function __construct(Version6CIDRNotation $validator)
    {

        $this->validator = $validator;
    }

    /**
     * @param $query
     * @return bool|int
     * @throws \Exception
     */
    public function calc($query)
    {
        $this->reset();
        if ($this->validateQuery($query) !== true) {
            return false;
        }

        return $this->calcSubnet();
    }

    /**
     * Reset all properties for this class
     *
     * @return $this
     */
    public function reset()
    {
        $this->errors = [];
        $this->segments = [];
        $this->address = null;
        $this->prefix = null;
        $this->network_range = [];
        $this->group = null;

        return $this;
    }

    /**
     * Returns an array with the calculated NetworkRange
     *
     * @return array
     */
    public function getNetworkRange()
    {
        return $this->network_range;
    }

    /**
     * Returns an array with error messages reported in the last run
     *
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errors;
    }

    protected function validateQuery($query)
    {
        try {
            $this->validator->isValid($query);
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();

            return false;
        }

        $this->address = $this->validator->getAddress();
        $this->prefix = $this->validator->getPrefix();

        return true;
    }

    /**
     * Calculates the network range based on the address and Prefix
     *
     * @return bool
     * @throws \Exception
     */
    protected function calcSubnet()
    {
        $binSubnet = $this->getBinSubnet();

        $binMin = str_pad($binSubnet, 16, '0', STR_PAD_RIGHT);
        $binMax = str_pad($binSubnet, 16, '1', STR_PAD_RIGHT);
        $hexMin = base_convert($binMin, 2, 16);
        $hexMax = base_convert($binMax, 2, 16);

        $segments_min = $segments_max = $this->segments;

        $segments_min[$this->group] = str_pad($hexMin, 4, '0', STR_PAD_LEFT);
        $segments_max[$this->group] = str_pad($hexMax, 4, '0', STR_PAD_LEFT);

        for ($i = ($this->group + 1); $i < 8; $i++) {
            $segments_min[$i] = '0000';
            $segments_max[$i] = 'ffff';
        }

        $this->network_range['min'] = implode(':', $segments_min);
        $this->network_range['max'] = implode(':', $segments_max);
        $this->network_range['hosts'] = pow(2, (128 - $this->prefix));

        return true;
    }

    protected function setProperties()
    {
        $this->isValidated();
        $this->segments = explode(':', $this->address);
        $this->group = (int)floor($this->prefix / 16);

        return $this;
    }

    public function isValidated($throw = true)
    {
        if (empty($this->address) || empty($this->prefix)) {
            if ($throw === false) {
                return false;
            } else {
                throw new \Exception('No validated values are available');
            }
        }

        return $this;
    }

    protected function getBinSubnet()
    {
        $this->isValidated();

        return substr($this->getBinGroupSegment(), 0, ($this->prefix % 16));
    }

    protected function getBinGroupSegment()
    {
        $decSegment = base_convert($this->getHexGroupSegment(), 16, 10);

        return sprintf("%016b", $decSegment);
    }

    protected function getHexGroupSegment()
    {
        if (is_null($this->group)) {
            $this->setProperties();
        }

        return $this->segments[$this->group];
    }
}
