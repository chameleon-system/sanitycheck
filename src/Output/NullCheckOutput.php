<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Output;

use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;

/**
 * NullCheckOutput is a dummy that can be used to avoid output with only minimal adjustments to configuration.
 */
class NullCheckOutput implements CheckOutputInterface
{
    /**
     * {@inheritdoc}
     */
    public function gather(CheckOutcome $outcome)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
    }
}
