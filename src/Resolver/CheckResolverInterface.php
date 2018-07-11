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

/**
 * CheckResolverInterface defines a service which can be used to discover sanity checks that
 * were previously registered through the service container.
 */
interface CheckResolverInterface
{
    /**
     * Finds checks that are identified by the given name.
     *
     * @param string $name A Symfony service identifier or a bundle name (e.g. "@AcmeDemoBundle")
     *
     * @return CheckInterface[]
     *
     * @throws CheckNotFoundException if there are no checks for this name
     */
    public function findChecksForName($name);

    /**
     * Finds checks for every name in the $name array. Each name may be a Symfony service identifier or a bundle name.
     *
     * @param array $nameList List of names to resolve (Symfony service identifiers or bundle names)
     *
     * @return CheckInterface[]
     *
     * @throws CheckNotFoundException if there are no checks for at least one name
     */
    public function findChecksForNameList(array $nameList);

    /**
     * Finds all checks defined in the application.
     *
     * @return CheckInterface[]
     */
    public function findAllChecks();
}
