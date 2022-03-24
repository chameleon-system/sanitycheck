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

use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;
use ChameleonSystem\SanityCheck\Translation\TranslatorFactory;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * AbstractTranslatingCheckOutput provides basic functionality for translating messages. Derive from this class if you
 * want to provide localized messages.
 */
abstract class AbstractTranslatingCheckOutput implements CheckOutputInterface
{
    /**
     * @var TranslatorInterface|null
     */
    protected $translator;
    /**
     * @var string
     */
    protected $translationDomain;

    /**
     * @param TranslatorInterface|null $translator
     * @param string                   $translationDomain
     * @param string|null              $locale
     */
    public function __construct(
        TranslatorInterface $translator = null,
        $translationDomain = 'chameleon_system_sanitycheck',
        $locale = null
    ) {
        if (null === $translator) {
            $translatorFactory = new TranslatorFactory();
            $this->translator = $translatorFactory->createTranslator($locale);
        } else {
            $this->translator = $translator;
        }
        $this->translationDomain = $translationDomain;

        if (null !== $locale) {
            $this->translator->setLocale($locale);
        }
    }

    /**
     * @param string $message
     * @param array  $parameters
     *
     * @return string
     */
    protected function translate($message, $parameters = array())
    {
        return $this->translator->trans($message, $parameters, $this->translationDomain);
    }

    /**
     * @param CheckOutcome $outcome
     *
     * @return string
     */
    protected function getTranslatedMessage(CheckOutcome $outcome)
    {
        return $this->translateOutcomeLevel($outcome->getLevel()).' - '.$this->translate(
            $outcome->getMessageKey(),
            $outcome->getMessageParameters()
        );
    }

    /**
     * @param int $level
     *
     * @return string
     */
    protected function translateOutcomeLevel($level)
    {
        return $this->translate('level.'.$level);
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->translator->setLocale($locale);
    }
}
