<?php
/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/21/15
 * Time: 5:39 PM
 */

namespace IPAddress\Filter\Version6;


class Version6Address
{

    public function filter($string)
    {
        $segments = $this->splitAddressString($string);

        return implode(':', $segments);
    }

    /**
     * Splits the Address String in 8 network groups
     * @param $string
     * @return array|bool
     * @throws \Exception
     */
    protected function splitAddressString($string)
    {
        if (empty($string)) {
            throw new \Exception('IP address string is empty');
        }

        $segments = explode(':', $string);
        $this->normalizeSegments($segments);

        return $segments;
    }

    /**
     * Normalizes given array into 8 complete groups with full 4 char hex string
     *
     * @param $segments
     * @return mixed
     */
    protected function normalizeSegments(&$segments)
    {
        $this->expandSegments($segments);

        foreach ($segments as $i => $segment) {
            if (strlen($segment) !== 4) {
                $segments[$i] = sprintf("%'04s", $segment);
            }
        }

        return $segments;
    }

    /**
     * Expands a given array and segments into 8 groups
     *
     * @param $segments
     * @return array
     * @throws \Exception
     */
    protected function expandSegments(&$segments)
    {
        if (($diff = $this->countSegmentsDiff($segments)) === 0) {
            return true;
        }

        $newSegments = [];
        foreach ($segments as $i => $segment) {
            if (empty($segment)) {
                for ($i = 0; $i <= $diff; $i++) {
                    $newSegments[] = '0000';
                }
                continue;
            }

            $newSegments[] = $segment;
        }

        // @codeCoverageIgnoreStart
        if ($this->countSegmentsDiff($newSegments) !== 0) {
            // this only exists to further protect the integrity of the code and the intended result
            // it should never actually throw
            throw new \Exception('Expanding IP segments failed for unknown reason');
        }
        // @codeCoverageIgnoreEnd

        return $segments = $newSegments;
    }

    protected function countSegmentsDiff($segments)
    {
        // @codeCoverageIgnoreStart
        if (empty($segments)) {
            // this only exists to further protect the integrity of the code and the intended result(s)
            // in the current version it should never actually throw
            throw new \Exception('IP address format has no segments');
        }
        // @codeCoverageIgnoreEnd

        if (count($segments) > 8) {
            throw new \Exception('IP address has too many segments');
        }
        $c = count($segments);

        return $diff = 8 - $c;
    }
}