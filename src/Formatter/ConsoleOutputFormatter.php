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

use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;

class ConsoleOutputFormatter implements OutputFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format($message, $level)
    {
        switch ($level) {
            case CheckOutcome::OK:
                return '<info>'.$message.'</info>';
            case CheckOutcome::NOTICE:
                return '<info>'.$message.'</info>';
            case CheckOutcome::WARNING:
                return '<comment>'.$message.'</comment>';
            case CheckOutcome::ERROR:
                return '<error>'.$message.'</error>';
            case CheckOutcome::EXCEPTION:
                return '<error>'.$message.'</error>';
            default:
                return '<error>'.$message.'</error>';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNewlineDelimiter()
    {
        return "\n";
    }
}
