<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Check;

use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;

/**
 * CheckInterface defines a common interface for all types of checks.
 */
interface CheckInterface
{
    /**
     * Perform this check.
     *
     * @return CheckOutcome[]
     */
    public function performCheck();

    /**
     * Returns the level of this check.
     *
     * @return int
     */
    public function getLevel();
}
