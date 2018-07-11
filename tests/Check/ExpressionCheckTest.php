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

class ExpressionCheckTest extends TestCase
{
    /**
     * @var ExpressionCheck
     */
    protected $expressionCheck;

    public function testTrue()
    {
        $expression = 'true === true';
        $this->expressionCheck = new ExpressionCheck(CheckOutcome::ERROR, array($expression));

        $actualOutcomeList = $this->expressionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.expression.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testFalse()
    {
        $expression = 'true === false';
        $this->expressionCheck = new ExpressionCheck(CheckOutcome::ERROR, array($expression));

        $actualOutcomeList = $this->expressionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.expression.nottrue', array('%0%' => $expression), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testMultiple()
    {
        $expressionList = array(
            'true === false',
            'true === true',
            '1 === 0',
        );
        $this->expressionCheck = new ExpressionCheck(CheckOutcome::ERROR, $expressionList);

        $actualOutcomeList = $this->expressionCheck->performCheck();

        $expectedOutcomeList = array(
            new CheckOutcome('check.expression.nottrue', array('%0%' => 'true === false'), CheckOutcome::ERROR),
            new CheckOutcome('check.expression.nottrue', array('%0%' => '1 === 0'), CheckOutcome::ERROR),
        );

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testInvalidExpression()
    {
        $expression = 'echo "wow"';
        $this->expressionCheck = new ExpressionCheck(CheckOutcome::ERROR, array($expression));

        $actualOutcomeList = $this->expressionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.expression.nottrue', array('%0%' => $expression), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }
}
