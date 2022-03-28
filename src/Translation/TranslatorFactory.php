<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Translation;

use ChameleonSystem\SanityCheck\Configuration\SanityCheckConfiguration;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatorFactory
{
    /**
     * @param string      $locale
     * @param string|null $class
     * @param string|null $cacheDir
     * @param bool        $debug
     *
     * @return TranslatorInterface
     */
    public function createTranslator($locale, $class = null, $cacheDir = null, $debug = false)
    {
        if (null === $class) {
            $class = '\Symfony\Component\Translation\Translator';
        }
        if (null === $locale) {
            $locale = 'en';
        }
        /** @var Translator $translator */
        $translator = new $class($locale, null, $cacheDir, $debug);
        $translator->addLoader('xml', new XliffFileLoader());
        $configuration = new SanityCheckConfiguration();
        $translations = $configuration->getTranslationResources();
        foreach ($translations as $locale => $resources) {
            foreach ($resources as $resource) {
                $translator->addResource('xml', $resource, $locale, 'chameleon_system_sanitycheck');
            }
        }

        return $translator;
    }
}
