<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Handler;

use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;

/**
 * CheckHandlerInterface Defines a service that performs sanity checks.
 */
interface CheckHandlerInterface
{
    /**
     * Performs all registered checks.
     *
     * @return CheckOutcome[]
     */
    public function checkAll();

    /**
     * Performs a check on the given check items. The parameter array contains
     * Adds an outcome on level ERROR if a check could not be found.
     *
     * @param string[] $checkNames check identifiers and/or bundle names
     *                             in camel-case bundle name (e.g. "@AcmeDemoBundle") or the bundle alias (e.g. "acme_demo").
     *
     * @return CheckOutcome[]
     */
    public function checkSome(array $checkNames);

    /**
     * Performs a check on the given check item. $checkName
     * Note that multiple CheckOutcomes may be returned depending on the check.
     * Adds an outcome on level ERROR if a check could not be found.
     *
     * @param string $checkName can be a check identifier or a bundle name
     *                          in camel-case (e.g. "@AcmeDemoBundle") or the bundle alias (e.g. "acme_demo").
     *
     * @return CheckOutcome[]
     */
    public function checkSingle($checkName);
}
