<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Helper;

class PhpVersionHelper
{
    /**
     * @param string|array $versions
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function parseVersions($versions)
    {
        $retValue = array();
        if (is_string($versions)) {
            $retValue[] = array($versions, '>=');
        } elseif (is_array($versions)) {
            foreach ($versions as $version) {
                if (is_string($version)) {
                    $retValue[] = array($version, '>=');
                } elseif (is_array($version)) {
                    $retValue[] = array($version[0], $version[1]);
                } else {
                    throw new \InvalidArgumentException('Invalid version. Needs to be a string or an array.');
                }
            }
        } else {
            throw new \InvalidArgumentException('Invalid version. Needs to be a string or an array.');
        }

        return $retValue;
    }
}
