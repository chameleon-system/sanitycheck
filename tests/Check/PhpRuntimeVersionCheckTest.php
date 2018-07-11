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

function phpversion()
{
    return PhpRuntimeVersionCheckTest::$phpversion;
}

class PhpRuntimeVersionCheckTest extends TestCase
{
    public static $phpversion;

    /**
     * @var PhpModuleLoadedCheck
     */
    protected $phpRuntimeVersionCheck;

    public function testSimpleOk()
    {
        $version = '5.3.0';

        $this->phpRuntimeVersionCheck = new PhpRuntimeVersionCheck(CheckOutcome::ERROR, $version);

        $actualOutcomeList = $this->phpRuntimeVersionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.php_version.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testSimpleNotOk()
    {
        $version = '5.4.0';

        $this->phpRuntimeVersionCheck = new PhpRuntimeVersionCheck(CheckOutcome::ERROR, $version);

        $actualOutcomeList = $this->phpRuntimeVersionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.php_version.invalidversion', array('%0%' => self::$phpversion), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testOperatorOk()
    {
        $version = array(array('6.0.0', '<'), '5.3.2', array('5.3.1', '!='));

        $this->phpRuntimeVersionCheck = new PhpRuntimeVersionCheck(CheckOutcome::ERROR, $version);

        $actualOutcomeList = $this->phpRuntimeVersionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.php_version.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testOperatorNotOk()
    {
        $version = array(array('5.3.0', '>='), array('5.3.9', '!='));

        $this->phpRuntimeVersionCheck = new PhpRuntimeVersionCheck(CheckOutcome::ERROR, $version);

        $actualOutcomeList = $this->phpRuntimeVersionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.php_version.invalidversion', array('%0%' => self::$phpversion), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testSimpleInvalidVersionType()
    {
        $version = 5.3;
        $this->expectException('\InvalidArgumentException');
        $this->phpRuntimeVersionCheck = new PhpRuntimeVersionCheck(CheckOutcome::ERROR, $version);
    }

    protected function setUp()
    {
        parent::setUp();
        self::$phpversion = '5.3.9';
    }
}
