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
use ChameleonSystem\SanityCheck\Exception\CheckNotFoundException;
use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;
use ChameleonSystem\SanityCheck\Resolver\CheckResolverInterface;

/**
 * {@inheritdoc}
 */
class CheckHandler implements CheckHandlerInterface
{
    /**
     * @var CheckResolverInterface
     */
    private $checkResolver;

    /**
     * @param CheckResolverInterface $checkResolver
     */
    public function __construct(CheckResolverInterface $checkResolver)
    {
        $this->checkResolver = $checkResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function checkAll()
    {
        $checkList = $this->checkResolver->findAllChecks();
        $retValue = $this->doChecks($checkList);

        return $retValue;
    }

    /**
     * {@inheritdoc}
     */
    public function checkSome(array $checkNames)
    {
        try {
            $checkList = $this->checkResolver->findChecksForNameList($checkNames);
            $retValue = $this->doChecks($checkList);
        } catch (CheckNotFoundException $e) {
            $retValue = array(
                new CheckOutcome(
                    'message.checknotfound',
                    array('%0%' => $e->getMessage()),
                    CheckOutcome::ERROR
                ),
            );
        }

        return $retValue;
    }

    /**
     * {@inheritdoc}
     */
    public function checkSingle($checkName)
    {
        try {
            $checkList = $this->checkResolver->findChecksForName($checkName);
            $retValue = $this->doChecks($checkList);
        } catch (CheckNotFoundException $e) {
            $retValue = array(
                new CheckOutcome(
                    'message.checknotfound',
                    array('%0%' => $e->getMessage()),
                    CheckOutcome::ERROR
                ),
            );
        }

        return $retValue;
    }

    /**
     * Does the actual work on the concrete and configured checks.
     *
     * @param $checkList [CheckInterface]
     *
     * @return CheckOutcome[]
     */
    private function doChecks(array $checkList)
    {
        $retValue = array();
        if (empty($checkList)) {
            $retValue[] = new CheckOutcome('message.nochecks', array(), CheckOutcome::NOTICE);

            return $retValue;
        }

        /** @var CheckInterface $check */
        foreach ($checkList as $check) {
            try {
                $retValue = array_merge($retValue, $check->performCheck());
            } catch (\Exception $e) {
                $retValue[] = new CheckOutcome(
                    'message.exception',
                    array('%0%' => $e->getMessage()),
                    CheckOutcome::EXCEPTION
                );
            }
        }
        if (empty($retValue)) {
            $retValue[] = new CheckOutcome('message.nooutcome', array(), CheckOutcome::NOTICE);
        }

        return $retValue;
    }
}
