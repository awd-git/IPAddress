<?php
/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/22/15
 * Time: 12:30 PM
 */

namespace IPAddress\Validator\Version6;


class Version6CIDRNotation
{

    /**
     * @var string
     */
    protected $address;

    /**
     * @var int
     */
    protected $prefix;

    protected $validated = [];

    /**
     * @var Version6Address
     */
    private $addressValidator;
    /**
     * @var Version6Prefix
     */
    private $prefixValidator;

    public function __construct(Version6Address $addressValidator, Version6Prefix $prefixValidator)
    {

        $this->addressValidator = $addressValidator;
        $this->prefixValidator = $prefixValidator;
    }

    public function isValid($value)
    {
        $this->reset();
        $this->validateQueryString($value)->setProperties($value)->validateAddress()->validatePrefix();

        return true;
    }

    public function reset()
    {
        $this->address = null;
        $this->prefix = null;
        $this->validated = [];
    }

    /**
     * Gets the address part of a valid CIDR notation
     *
     * @return string
     * @throws \Exception
     */
    public function getAddress()
    {
        if (is_null(@$this->validated['address'])) {
            throw new \Exception('No validated IP address exists');
        }

        return $this->validated['address'];
    }

    /**
     * Gets the prefix part of a valid CIDR notation
     *
     * @return int
     * @throws \Exception
     */
    public function getPrefix()
    {
        if (is_null(@$this->validated['prefix'])) {
            throw new \Exception('No validated prefix exists');
        }

        return $this->validated['prefix'];
    }

    /**
     * Validates basic format for the submitted query string
     *
     * @param string $string
     * @return $this
     * @throws \Exception
     */
    protected function validateQueryString($string)
    {
        if (empty($string)) {
            throw new \Exception('Query string is empty');
        } elseif (strlen($string) > (4 * 8) + 11) {
            throw new \Exception('Query string is too long');
        }

        return $this;
    }

    /**
     * Validates the address part of the notation
     *
     * @return $this
     * @throws \Exception
     */
    protected function validateAddress()
    {
        $this->addressValidator->isValid($this->address);
        $this->validated['address'] = $this->addressValidator->getFormattedAddress();

        return $this;
    }

    /**
     * Validates the network prefix part of the notation
     *
     * @return $this
     */
    protected function validatePrefix()
    {
        $this->prefixValidator->isValid($this->prefix);
        $this->validated['prefix'] = $this->prefixValidator->getPrefix();

        return $this;
    }

    /**
     * Sets Address and Prefix property values
     *
     * @param string $string
     * @return $this
     * @throws \Exception
     */
    protected function setProperties($string)
    {
        $list = explode('/', $string);
        if (count($list) !== 2) {
            throw new \Exception('CIDR notation format is not valid');
        }

        $this->address = $list[0];
        $this->prefix = $list[1];

        return $this;
    }

}