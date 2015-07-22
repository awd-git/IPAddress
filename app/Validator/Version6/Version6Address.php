<?php
/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/21/15
 * Time: 5:25 PM
 */

namespace IPAddress\Validator\Version6;


use IPAddress\Filter\Version6\Version6Address as Version6Filter;

class Version6Address
{

    protected $ip;
    protected $segments;
    /**
     * @var Version6Filter
     */
    private $filter;

    public function __construct(Version6Filter $filter)
    {

        $this->filter = $filter;
    }

    /**
     * Validates the IP property value if it is a valid IPv6 string
     *
     * @return bool
     * @throws \Exception
     */
    public function isValid($value)
    {
        $this->ip = null;
        $this->validateQueryString($value);

        // @codeCoverageIgnoreStart
        if (!$this->validateAddressFormat($value)) {
            // this only exists to further protect the integrity of the code and the intended result
            // Version6Filter should handle any errors and throw exceptions
            throw new \Exception('IP address format is not valid');
        }

        // @codeCoverageIgnoreEnd

        return true;
    }

    public function getFormattedAddress()
    {
        if (empty($this->ip)) {
            throw new \Exception('No validated IP address exists');
        }

        return $this->ip;
    }

    /**
     * Validates basic format for the submitted query string
     *
     * @param $string
     * @return bool
     * @throws \Exception
     */
    protected function validateQueryString($string)
    {
        if (!is_string($string)) {
            throw new \Exception('IP address is not a string');
        } elseif (strlen($string) > ((4 * 8) + 7)) {
            throw new \Exception('IP address string is too long');
        }

        return true;
    }

    /**
     * Validates the IP address format and splits in segments into groups
     *
     * @param $address
     * @return bool
     * @throws \Exception
     */
    protected function validateAddressFormat($address)
    {
        $this->ip = $this->filter->filter($address);

        return true;
    }

}