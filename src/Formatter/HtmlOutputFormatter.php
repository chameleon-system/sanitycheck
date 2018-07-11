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

class HtmlOutputFormatter implements OutputFormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format($message, $level)
    {
        switch ($level) {
            case CheckOutcome::OK:
                return '<span class="sanity_check_info">'.$message.'</span>';
            case CheckOutcome::NOTICE:
                return '<span class="sanity_check_info">'.$message.'</span>';
            case CheckOutcome::WARNING:
                return '<span class="sanity_check_warning">'.$message.'</span>';
            case CheckOutcome::ERROR:
                return '<span class="sanity_check_error">'.$message.'</span>';
            case CheckOutcome::EXCEPTION:
                return '<span class="sanity_check_error">'.$message.'</span>';
            default:
                return '<span class="sanity_check_error">'.$message.'</span>';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNewlineDelimiter()
    {
        return "<br />\n";
    }
}
