<?php
/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/22/15
 * Time: 12:59 PM
 */

namespace IPAddress\Validator\Version6;


class Version6Prefix
{
    protected $prefix;

    /**
     * Validates the Prefix property value if it is a valid CIDR notation
     *
     * @return bool
     */
    public function isValid($value)
    {
        $this->validateQueryString($value);
        $this->prefix = $value;
        return true;
    }

    public function getPrefix() {
        return $this->prefix;
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
        if (empty($string)) {
            throw new \Exception('Prefix string is empty');
        } elseif (!is_numeric($string)) {
            throw new \Exception('Prefix string is not numeric');
        } elseif ($string < 1 || $string > 128) {
            throw new \Exception('Prefix string is not a valid range');
        }

        return true;
    }
}