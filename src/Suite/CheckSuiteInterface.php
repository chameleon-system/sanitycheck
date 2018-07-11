<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Suite;

use ChameleonSystem\SanityCheck\Output\CheckOutputInterface;

/**
 * CheckSuiteInterface defines a service which is meant to simplify the execution of a check.
 * Implementations should provide a functionality to be configured with checks and outputs, and then handle
 * the checks and write the outcomes when the execute() method is called.
 */
interface CheckSuiteInterface
{
    /**
     * Executes the defined checks and writes their output to the defined check outputs.
     */
    public function execute();

    /**
     * Gets a list of check outputs. This way, they can be modified by a caller before execution.
     *
     * @return CheckOutputInterface[]
     */
    public function getOutputs();

    /**
     * Sets a list of check outputs. This is especially useful in a console command to redirect output
     * to console.
     *
     * @param array $checkOutputs
     *
     * @return mixed
     */
    public function setOutputs(array $checkOutputs);
}
