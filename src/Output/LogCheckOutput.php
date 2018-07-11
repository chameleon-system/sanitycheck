<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Output;

use ChameleonSystem\SanityCheck\Formatter\OutputFormatterInterface;
use ChameleonSystem\SanityCheck\Formatter\PlainOutputFormatter;
use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * LogCheckOutput is used to write a CheckOutcome to a log file.
 */
class LogCheckOutput extends AbstractTranslatingCheckOutput
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var OutputFormatterInterface
     */
    private $outputFormatter;

    /**
     * @param OutputFormatterInterface $outputFormatter
     * @param LoggerInterface|null     $logger
     * @param TranslatorInterface      $translator
     * @param string                   $translationDomain
     */
    public function __construct(
        OutputFormatterInterface $outputFormatter = null,
        LoggerInterface $logger = null,
        TranslatorInterface $translator = null,
        $translationDomain = 'chameleon_system_sanitycheck'
    ) {
        parent::__construct($translator, $translationDomain);
        if (null === $outputFormatter) {
            $this->outputFormatter = new PlainOutputFormatter();
        } else {
            $this->outputFormatter = $outputFormatter;
        }
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function gather(CheckOutcome $outcome)
    {
        $message = $this->getTranslatedMessage($outcome);
        $line = $this->outputFormatter->format($message, $outcome->getLevel());
        if (null === $this->logger) {
            echo $line."\n";
        } else {
            $this->logger->log($this->getLogLevel($outcome->getLevel()), $line);
        }
    }

    /**
     * Translates a given check level to a log level.
     *
     * @param int $outcomeLevel the outcome level
     *
     * @return string A log level as defined in Psr\Log\LogLevel
     */
    protected function getLogLevel($outcomeLevel)
    {
        switch ($outcomeLevel) {
            case CheckOutcome::OK:
                return LogLevel::INFO;
            case CheckOutcome::NOTICE:
                return LogLevel::NOTICE;
            case CheckOutcome::WARNING:
                return LogLevel::WARNING;
            case CheckOutcome::ERROR:
                return LogLevel::ERROR;
            case CheckOutcome::EXCEPTION:
                return LogLevel::ERROR;
            default:
                return LogLevel::ERROR;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
    }
}
