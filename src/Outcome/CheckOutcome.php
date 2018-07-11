<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Outcome;

/**
 * CheckOutcome defines the return value of a Check.
 */
class CheckOutcome
{
    /**
     * The constants are semantically ordered from low to high, so you can react to outcomes that are
     * "at least as severe as", e.g. "display all warnings and errors" can be expressed by ">= CheckOutcome::WARNING".
     * Every constant should be between 10 and 99 so that they have an equal length in a log file (if not translated).
     */
    const OK = 10;
    const NOTICE = 20;
    const WARNING = 30;
    const ERROR = 40;
    const EXCEPTION = 80;

    public static $LEVELS = array(self::OK, self::NOTICE, self::WARNING, self::ERROR, self::EXCEPTION);

    /**
     * @var string The lookup key of the message to display/log
     */
    private $messageKey;
    /**
     * @var string[] Optional parameters to insert into the translated message
     */
    private $messageParameters;
    /**
     * @var number The machine-readable outcome of the check. This is one of the constants in this class.
     *             The interpretation of the outcome is left to the caller.
     */
    private $level;

    /**
     * @param string $messageKey
     * @param array  $messageParameters
     * @param int    $level
     */
    public function __construct($messageKey, array $messageParameters, $level)
    {
        $this->messageKey = $messageKey;
        $this->messageParameters = $messageParameters;
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getMessageKey()
    {
        return $this->messageKey;
    }

    /**
     * @return array
     */
    public function getMessageParameters()
    {
        return $this->messageParameters;
    }
}
