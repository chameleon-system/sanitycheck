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
use PHPUnit\Framework\TestCase;

function extension_loaded($extension)
{
    return PhpModuleLoadedCheckTest::$extensionLoaded[$extension];
}

class PhpModuleLoadedCheckTest extends TestCase
{
    public static $extensionLoaded;

    /**
     * @var PhpModuleLoadedCheck
     */
    protected $phpModuleLoadedCheck;

    public function testTrue()
    {
        $module = 'very_module';
        self::$extensionLoaded[$module] = true;

        $this->phpModuleLoadedCheck = new PhpModuleLoadedCheck(CheckOutcome::ERROR, array($module));

        $actualOutcomeList = $this->phpModuleLoadedCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.php_module_loaded.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testFalse()
    {
        $module = 'very_module';
        self::$extensionLoaded[$module] = false;

        $this->phpModuleLoadedCheck = new PhpModuleLoadedCheck(CheckOutcome::ERROR, array($module));

        $actualOutcomeList = $this->phpModuleLoadedCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.php_module_loaded.notloaded', array('%0%' => $module), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testMultiple()
    {
        $module1 = 'very_module';
        $module2 = 'such_module';
        $module3 = 'wow_module';
        $moduleList = array($module1, $module2, $module3);
        self::$extensionLoaded[$module1] = false;
        self::$extensionLoaded[$module2] = true;
        self::$extensionLoaded[$module3] = false;

        $this->phpModuleLoadedCheck = new PhpModuleLoadedCheck(CheckOutcome::ERROR, $moduleList);

        $actualOutcomeList = $this->phpModuleLoadedCheck->performCheck();

        $expectedOutcomeList = array(
            new CheckOutcome('check.php_module_loaded.notloaded', array('%0%' => $module1), CheckOutcome::ERROR),
            new CheckOutcome('check.php_module_loaded.notloaded', array('%0%' => $module3), CheckOutcome::ERROR),
        );

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    protected function setUp()
    {
        parent::setUp();
        self::$extensionLoaded = array();
    }
}
