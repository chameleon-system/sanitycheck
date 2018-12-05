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
 * Defines a service which can be used to discover sanity checks. How checks are discovered is up to implementations.
 */
interface CheckResolverInterface
{
    /**
     * Finds checks that are identified by the given name.
     *
     * @param string $name Name to resolve
     *
     * @return CheckInterface[]
     *
     * @throws CheckNotFoundException if there are no checks for this name
     */
    public function findChecksForName($name);

    /**
     * Finds checks for every name in the $name array.
     *
     * @param array $nameList List of names to resolve
     *
     * @return CheckInterface[]
     *
     * @throws CheckNotFoundException if there are no checks for at least one name
     */
    public function findChecksForNameList(array $nameList);

    /**
     * Finds all checks in the scope of this resolver.
     *
     * @return CheckInterface[]
     */
    public function findAllChecks();
}
