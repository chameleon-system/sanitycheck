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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * DefaultCheckOutput is used to echo a CheckOutcome to the current output (usually browser or console).
 */
class DefaultCheckOutput extends AbstractTranslatingCheckOutput
{
    /**
     * @var OutputFormatterInterface
     */
    private $outputFormatter;
    /**
     * @var bool
     */
    private $doBuffer;
    /**
     * @var array
     */
    private $buffer = array();

    /**
     * @param OutputFormatterInterface|null $outputFormatter
     * @param bool                          $doBuffer
     * @param TranslatorInterface|null      $translator
     * @param string                        $translationDomain
     */
    public function __construct(
        OutputFormatterInterface $outputFormatter = null,
        $doBuffer = false,
        TranslatorInterface $translator = null,
        $translationDomain = 'chameleon_system_sanitycheck'
    ) {
        parent::__construct($translator, $translationDomain);
        if (null === $outputFormatter) {
            $this->outputFormatter = new PlainOutputFormatter();
        } else {
            $this->outputFormatter = $outputFormatter;
        }
        $this->doBuffer = $doBuffer;
    }

    /**
     * {@inheritdoc}
     */
    public function gather(CheckOutcome $outcome)
    {
        $message = $this->getTranslatedMessage($outcome);
        $line = $this->outputFormatter->format($message, $outcome->getLevel());
        $line .= $this->outputFormatter->getNewlineDelimiter();
        if ($this->doBuffer) {
            $this->buffer[] = $line;
        } else {
            echo $line;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        if ($this->doBuffer) {
            foreach ($this->buffer as $line) {
                echo $line;
            }
            $this->buffer = array();
        }
    }

    /**
     * @param OutputFormatterInterface $outputFormatter
     */
    public function setOutputFormatter($outputFormatter)
    {
        $this->outputFormatter = $outputFormatter;
    }
}
