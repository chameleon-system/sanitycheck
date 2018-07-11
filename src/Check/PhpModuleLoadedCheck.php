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
 * PhpModuleLoadedCheck verifies that given PHP modules are loaded.
 * It will return Outcomes on the given level if one or more
 * modules are missing.
 */
class PhpModuleLoadedCheck extends AbstractCheck
{
    /**
     * @var array
     */
    private $modules;

    /**
     * @param int   $level
     * @param array $modules
     */
    public function __construct($level, array $modules)
    {
        parent::__construct($level);
        $this->modules = $modules;
    }

    /**
     * {@inheritdoc}
     */
    public function performCheck()
    {
        $retValue = array();
        foreach ($this->modules as $module) {
            if (!extension_loaded($module)) {
                $retValue[] = new CheckOutcome(
                    'check.php_module_loaded.notloaded',
                    array('%0%' => $module),
                    $this->getLevel()
                );
            }
        }

        if (empty($retValue)) {
            $retValue[] = new CheckOutcome('check.php_module_loaded.ok', array(), CheckOutcome::OK);
        }

        return $retValue;
    }
}
