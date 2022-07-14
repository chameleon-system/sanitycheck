<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Handler;

use ChameleonSystem\SanityCheck\Check\CheckInterface;
use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;
use ChameleonSystem\SanityCheck\Resolver\CheckResolverInterface;
use PHPUnit\Framework\TestCase;

class CheckHandlerTest extends TestCase
{
    /**
     * @var CheckHandler $checkHandler
     */
    protected $checkHandler;
    /**
     * @var CheckResolverInterface $checkResolver ;
     */
    protected $checkResolver;

    /**
     * @var CheckInterface $checkOk
     */
    protected $checkOk;

    /**
     * @var CheckInterface $checkWarning
     */
    protected $checkWarning;

    /**
     * @var CheckInterface $checkException
     */
    protected $checkException;

    /**
     * @var CheckInterface $checkNoOutcome
     */
    protected $checkNoOutcome;

    /**
     * @var CheckOutcome[]
     */
    protected $irregularOutcomes = array();

    public function testSingleCheckOk()
    {
        $this->doSingleCheck($this->checkOk);
    }

    public function testSingleCheckWarning()
    {
        $this->doSingleCheck($this->checkWarning);
    }

    public function testSingleCheckException()
    {
        $revealedCheck = $this->checkException->reveal();
        $this->checkResolver->findChecksForName('chameleon_sanitycheck.testcheck')->willReturn(array($revealedCheck));

        $outcomeList = $this->checkHandler->checkSingle('chameleon_sanitycheck.testcheck');

        $this->assertCount(1, $outcomeList);
        $this->assertEquals('message.exception', $outcomeList[0]->getMessageKey());
        $this->assertEquals(CheckOutcome::EXCEPTION, $outcomeList[0]->getLevel());
    }

    public function testSingleCheckNoOutcome()
    {
        $revealedCheck = $this->checkNoOutcome->reveal();
        $this->checkResolver->findChecksForName('chameleon_sanitycheck.testcheck')->willReturn(array($revealedCheck));

        $outcomeList = $this->checkHandler->checkSingle('chameleon_sanitycheck.testcheck');

        $this->assertCount(1, $outcomeList);
        $this->assertEquals($this->irregularOutcomes[2]->getMessageKey(), $outcomeList[0]->getMessageKey());
        $this->assertEquals($this->irregularOutcomes[2]->getLevel(), $outcomeList[0]->getLevel());
    }

    public function doSingleCheck($check)
    {
        $revealedCheck = $check->reveal();
        $expectedOutomeList = $revealedCheck->performCheck();

        $this->checkResolver->findChecksForName('chameleon_sanitycheck.testcheck')->willReturn(array($revealedCheck));

        $outcomeList = $this->checkHandler->checkSingle('chameleon_sanitycheck.testcheck');

        $this->assertCount(1, $outcomeList);
        $this->assertEquals($expectedOutomeList[0]->getMessageKey(), $outcomeList[0]->getMessageKey());
        $this->assertEquals($expectedOutomeList[0]->getLevel(), $outcomeList[0]->getLevel());
    }

    public function testSingleCheckNotRegistered()
    {
        $this->checkResolver->findChecksForName('chameleon_sanitycheck.testcheck')->willThrow('\ChameleonSystem\SanityCheck\Exception\CheckNotFoundException');

        $outcomeList = $this->checkHandler->checkSingle('chameleon_sanitycheck.testcheck');

        $this->assertCount(1, $outcomeList);
        $this->assertEquals($this->irregularOutcomes[1]->getMessageKey(), $outcomeList[0]->getMessageKey());
    }

    public function testSomeChecks()
    {
        $this->doSomeChecks(array($this->checkOk, $this->checkWarning));
    }

    public function testSomeChecksNotRegistered()
    {
        $checkData = array('chameleon_sanitycheck.testcheck');
        $this->checkResolver->findChecksForNameList($checkData)->willThrow('\ChameleonSystem\SanityCheck\Exception\CheckNotFoundException');

        $outcomeList = $this->checkHandler->checkSome($checkData);

        $this->assertCount(1, $outcomeList);
        $this->assertEquals($this->irregularOutcomes[1]->getMessageKey(), $outcomeList[0]->getMessageKey());
    }

    public function testAll()
    {
        $checkList = array($this->checkOk, $this->checkWarning);
        $revealedCheckList = array();
        $expectedOutcomeList = array();
        foreach ($checkList as $check) {
            $revealedCheck = $check->reveal();
            $revealedCheckList[] = $revealedCheck;
            $expectedOutcomeList[] = $revealedCheck->performCheck();
        }

        $this->checkResolver->findAllChecks()->willReturn($revealedCheckList);

        $outcomeList = $this->checkHandler->checkAll();

        $this->assertCount(2, $outcomeList);
        $this->assertEquals($expectedOutcomeList[0][0]->getMessageKey(), $outcomeList[0]->getMessageKey());
        $this->assertEquals($expectedOutcomeList[0][0]->getLevel(), $outcomeList[0]->getLevel());
        $this->assertEquals($expectedOutcomeList[1][0]->getMessageKey(), $outcomeList[1]->getMessageKey());
        $this->assertEquals($expectedOutcomeList[1][0]->getLevel(), $outcomeList[1]->getLevel());
    }

    public function testAllNoneRegistered()
    {
        $this->checkResolver->findAllChecks()->willReturn(array());

        $outcomeList = $this->checkHandler->checkAll();

        $this->assertCount(1, $outcomeList);
        $this->assertEquals($this->irregularOutcomes[0]->getMessageKey(), $outcomeList[0]->getMessageKey());
        $this->assertEquals($this->irregularOutcomes[0]->getLevel(), $outcomeList[0]->getLevel());
    }

    public function doSomeChecks($checkList)
    {
        $revealedCheckList = array();
        $expectedOutcomeList = array();
        foreach ($checkList as $check) {
            $revealedCheck = $check->reveal();
            $revealedCheckList[] = $revealedCheck;
            $expectedOutcomeList[] = $revealedCheck->performCheck();
        }

        $this->checkResolver->findChecksForNameList(
            array('chameleon_sanitycheck.testcheck1', 'chameleon_sanitycheck.testcheck2')
        )->willReturn($revealedCheckList);

        $outcomeList = $this->checkHandler->checkSome(
            array('chameleon_sanitycheck.testcheck1', 'chameleon_sanitycheck.testcheck2')
        );

        $this->assertCount(2, $outcomeList);
        $this->assertEquals($expectedOutcomeList[0][0]->getMessageKey(), $outcomeList[0]->getMessageKey());
        $this->assertEquals($expectedOutcomeList[0][0]->getLevel(), $outcomeList[0]->getLevel());
        $this->assertEquals($expectedOutcomeList[1][0]->getMessageKey(), $outcomeList[1]->getMessageKey());
        $this->assertEquals($expectedOutcomeList[1][0]->getLevel(), $outcomeList[1]->getLevel());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->checkResolver = $this->prophesize(
            'ChameleonSystem\SanityCheck\Resolver\CheckResolverInterface'
        );
        $this->checkHandler = new CheckHandler($this->checkResolver->reveal());

        $this->checkOk = $this->prophesize('ChameleonSystem\SanityCheck\Check\CheckInterface');
        $this->checkOk->performCheck()->willReturn(
            array(new CheckOutcome('check.outcome.testok', array(), CheckOutcome::OK))
        );

        $this->checkWarning = $this->prophesize('ChameleonSystem\SanityCheck\Check\CheckInterface');
        $this->checkWarning->performCheck()->willReturn(
            array(new CheckOutcome('check.outcome.testwarning', array(), CheckOutcome::WARNING))
        );

        $this->checkException = $this->prophesize('ChameleonSystem\SanityCheck\Check\CheckInterface');
        $this->checkException->performCheck()->willThrow('\InvalidArgumentException');

        $this->checkNoOutcome = $this->prophesize('ChameleonSystem\SanityCheck\Check\CheckInterface');
        $this->checkNoOutcome->performCheck()->willReturn(array());

        $this->irregularOutcomes[] = new CheckOutcome('message.nochecks', array(), CheckOutcome::NOTICE);
        $this->irregularOutcomes[] = new CheckOutcome('message.checknotfound', array(), CheckOutcome::ERROR);
        $this->irregularOutcomes[] = new CheckOutcome('message.nooutcome', array(), CheckOutcome::NOTICE);
    }
}
