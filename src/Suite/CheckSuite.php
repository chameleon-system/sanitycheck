<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Suite;

use ChameleonSystem\SanityCheck\Handler\CheckHandlerInterface;
use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;
use ChameleonSystem\SanityCheck\Output\CheckOutputInterface;
use ChameleonSystem\SanityCheck\Resolver\OutputResolverInterface;

/**
 * Class CheckSuite
 * {@inheritdoc}
 */
class CheckSuite implements CheckSuiteInterface
{
    /**
     * @var CheckHandlerInterface
     */
    private $checkHandler;
    /**
     * @var OutputResolverInterface
     */
    private $outputResolver;
    /**
     * @var int
     */
    private $level;
    /**
     * @var CheckOutputInterface[]
     */
    private $outputs = array();
    /**
     * @var string[]
     */
    private $checks;

    /**
     * @param CheckHandlerInterface   $checkHandler
     * @param OutputResolverInterface $outputResolver
     * @param int                     $level
     * @param string|string[]         $outputs
     * @param string[]                $checks
     */
    public function __construct(
        CheckHandlerInterface $checkHandler,
        OutputResolverInterface $outputResolver,
        $level,
        $outputs,
        array $checks
    ) {
        $this->checkHandler = $checkHandler;
        $this->outputResolver = $outputResolver;
        $this->level = $level;
        $this->checks = $checks;

        if (is_array($outputs)) {
            foreach ($outputs as $level => $alias) {
                $this->outputs[$level] = $this->outputResolver->get($alias);
            }
        } else {
            $this->outputs[CheckOutcome::OK] = $this->outputResolver->get($outputs);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $outcomeList = $this->checkHandler->checkSome($this->checks);
        /** @var CheckOutputInterface[] $outputList */
        $outputList = array();

        foreach ($outcomeList as $outcome) {
            if ($outcome->getLevel() >= $this->level) {
                $outputsToUse = $this->resolveOutputs($outcome->getLevel());
                foreach ($outputsToUse as $output) {
                    $outputList[] = $output;
                    $output->gather($outcome);
                }
            }
        }
        foreach ($outputList as $output) {
            $output->commit();
        }
    }

    /**
     * Returns all outputs that are defined to handle a given $checkLevel ($checkLevel is equal or lower).
     *
     * @param $checkLevel
     *
     * @return CheckOutputInterface[]
     */
    protected function resolveOutputs($checkLevel)
    {
        $outputsToReturn = array();
        foreach ($this->outputs as $outputLevel => $output) {
            if ($checkLevel >= $outputLevel) {
                $outputsToReturn[] = $output;
            }
        }

        return $outputsToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputs()
    {
        return $this->outputs;
    }

    /**
     * {@inheritdoc}
     */
    public function setOutputs(array $checkOutputs)
    {
        $this->outputs = $checkOutputs;
    }
}
