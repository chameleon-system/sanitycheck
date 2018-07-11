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
 * ExpressionCheck checks if a given expression evaluates to true. Use this check sparsely, as you will only be able to provide
 * quite an abstract message if the expression isn't valid. Also be careful on which expressions are used - they are
 * evaluated using the eval statement, and there is no input sanitation (no user input is used, so we see that as a
 * valid use case for eval).
 */
class ExpressionCheck extends AbstractCheck
{
    /**
     * @var array
     */
    private $expressions;

    /**
     * @param int   $level
     * @param array $expressions
     */
    public function __construct($level, array $expressions)
    {
        parent::__construct($level);
        $this->expressions = $expressions;
    }

    /**
     * {@inheritdoc}
     */
    public function performCheck()
    {
        $retValue = array();
        $parseErrorClassExists = class_exists(\ParseError::class);
        foreach ($this->expressions as $expression) {
            if (true === $parseErrorClassExists) {
                try {
                    if (false === eval('return '.$expression.';')) {
                        $retValue[] = new CheckOutcome('check.expression.nottrue', array('%0%' => $expression), $this->getLevel());
                    }
                } catch (\ParseError $e) {
                    $retValue[] = new CheckOutcome('check.expression.nottrue', array('%0%' => $expression), $this->getLevel());
                }

            } else {
                if (!@eval('return '.$expression.';')) {
                    $retValue[] = new CheckOutcome('check.expression.nottrue', array('%0%' => $expression), $this->getLevel());
                }
            }
        }

        if (empty($retValue)) {
            $retValue[] = new CheckOutcome('check.expression.ok', array(), CheckOutcome::OK);
        }

        return $retValue;
    }
}
