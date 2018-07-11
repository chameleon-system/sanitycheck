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
 * FileExistsCheck checks if a given file set exists. If the $basePath is given, the files are always
 * expected under a common base directory (set $basePath to null if the $files are absolute paths).
 */
class FileExistsCheck extends AbstractCheck
{
    /**
     * @var string|null
     */
    private $basePath;
    /**
     * @var array
     */
    private $files;

    /**
     * @param int   $level
     * @param array $files
     * @param null  $basePath
     */
    public function __construct($level, array $files, $basePath = null)
    {
        parent::__construct($level);
        $this->basePath = $basePath;
        $this->files = $files;
    }

    /**
     * {@inheritdoc}
     */
    public function performCheck()
    {
        $retValue = array();
        $isOk = true;
        foreach ($this->files as $file) {
            if (null === $this->basePath) {
                $filePath = $file;
            } else {
                $filePath = $this->basePath.'/'.$file;
            }
            if (!$this->checkFileExists($filePath)) {
                $retValue[] = new CheckOutcome('check.file_exists.notexists', array('%0%' => $filePath), $this->getLevel());
                $isOk = false;
                continue;
            }
        }

        if ($isOk) {
            $retValue[] = new CheckOutcome('check.file_exists.ok', array(), CheckOutcome::OK);
        }

        return $retValue;
    }

    private function checkFileExists($filePath)
    {
        return file_exists($filePath);
    }
}
