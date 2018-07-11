<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Formatter;

interface OutputFormatterInterface
{
    /**
     * Formats the given $message, using the $level to improve differentiation.
     *
     * @param string $message
     * @param int    $level
     *
     * @return string
     */
    public function format($message, $level);

    /**
     * Returns a string that is appended at the end of each line.
     *
     * @return string
     */
    public function getNewlineDelimiter();
}
