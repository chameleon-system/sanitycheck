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

/**
 * AbstractCheck provides basic outcome level handling.
 */
abstract class AbstractCheck implements CheckInterface
{
    /**
     * @var int $level
     */
    private $level;

    /**
     * @param int $level
     */
    public function __construct($level)
    {
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }
}
