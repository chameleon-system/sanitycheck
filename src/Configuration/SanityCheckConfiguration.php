<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Configuration;

class SanityCheckConfiguration
{
    /**
     * @return array
     */
    public function getTranslationResources()
    {
        $translationDir = __DIR__.'/../../Resources/translations';
        $fileList = scandir($translationDir);
        $retValue = array();

        foreach ($fileList as $file) {
            if ('.' === substr($file, 0, 1)) {
                continue;
            }
            $locale = substr($file, strpos($file, '.') + 1, 2);
            $filePath = realpath($translationDir.DIRECTORY_SEPARATOR.$file);
            if (isset($retValue[$locale])) {
                $localeResources = $retValue[$locale];
            } else {
                $localeResources = array();
            }
            $localeResources[] = $filePath;
            $retValue[$locale] = $localeResources;
        }

        return $retValue;
    }
}
