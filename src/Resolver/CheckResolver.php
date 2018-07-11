<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Resolver;

use ChameleonSystem\SanityCheck\Check\CheckInterface;
use ChameleonSystem\SanityCheck\Exception\CheckNotFoundException;

class CheckResolver implements CheckResolverInterface
{
    /**
     * @var array
     */
    private $checks = array();

    /**
     * @param string         $name
     * @param CheckInterface $check
     */
    public function addCheck($name, CheckInterface $check)
    {
        $this->checks[$name] = $check;
    }

    /**
     * {@inheritdoc}
     */
    public function findChecksForName($name)
    {
        if (!isset($this->checks[$name])) {
            throw new CheckNotFoundException("Check with name '$name' not found.");
        }
        $retValue = array($this->checks[$name]);

        return $retValue;
    }

    /**
     * {@inheritdoc}
     */
    public function findChecksForNameList(array $nameList)
    {
        $retValue = array();
        foreach ($nameList as $name) {
            $retValue = array_merge($retValue, $this->findChecksForName($name));
        }

        return $retValue;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllChecks()
    {
        $retValue = array();
        foreach ($this->checks as $check) {
            $retValue[] = $check;
        }

        return $retValue;
    }
}
