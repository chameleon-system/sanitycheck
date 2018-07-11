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
 * CheckOutputInterface is a common interface for all kinds of CheckOutcome outputs.
 * The output process consists of two phases: First, all output information is gathered, and
 * then the output is committed to the output target. In simpler output scenarios you might also
 * do the complete handling in the gather method.
 */
interface CheckOutputInterface
{
    /**
     * This method can be implemented in two different ways:
     * - gather all information that is needed to write the given checkOutput, then write it later with the commit method.
     * - write the output directly if you want to avoid such buffering.
     *
     * @param CheckOutcome $outcome the outcome to write
     */
    public function gather(CheckOutcome $outcome);

    /**
     * This method is used to actually write outcomes. What it does depends on the implementation (might be an empty
     * implementation if the output it completely handled in the gather method; might also be the sending of an email
     * with the gathered data).
     * This method might be called even if there were no outcomes gathered that affect this output, and it's the method's
     * responsibility to handle that case.
     * This method MUST also reset its internal state, so that other checks in the same request can rely on a fresh data base.
     */
    public function commit();
}
