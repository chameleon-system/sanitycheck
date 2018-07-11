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
 * FilePermissionCheck checks permissions on the given files. If the $basePath is given, the files are always
 * expected under a common base directory (set $basePath to null if the $files are absolute paths).
 * The supported file permissions are READ, WRITE and EXECUTE.
 * This check will also return a CheckOutcome at the given level if a file does not exist.
 */
class FilePermissionCheck extends AbstractCheck
{
    const PERMISSION_READ = 'READ';
    const PERMISSION_WRITE = 'WRITE';
    const PERMISSION_EXECUTE = 'EXECUTE';

    /**
     * @var string|null
     */
    private $basePath;
    /**
     * @var array
     */
    private $files;
    /**
     * @var array
     */
    private $permissions;

    /**
     * @param int         $level
     * @param array       $files
     * @param array       $permissions
     * @param string|null $basePath
     */
    public function __construct($level, array $files, array $permissions, $basePath = null)
    {
        parent::__construct($level);
        $this->basePath = $basePath;
        $this->files = $files;
        $this->permissions = array_map('strtoupper', $permissions);
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
            foreach ($this->permissions as $permission) {
                $isOk &= $this->checkPermission($retValue, $filePath, $permission);
            }
        }

        if ($isOk) {
            $retValue[] = new CheckOutcome('check.file_permission.ok', array(), CheckOutcome::OK);
        }

        return $retValue;
    }

    /**
     * @param array  $retValue
     * @param string $filePath
     * @param string $permission
     *
     * @return bool
     */
    private function checkPermission(array &$retValue, $filePath, $permission)
    {
        $isOk = true;
        switch ($permission) {
            case self::PERMISSION_READ:
                if (!$this->checkReadable($filePath)) {
                    $retValue[] = new CheckOutcome(
                        'check.file_permission.notreadable',
                        array('%0%' => $filePath),
                        $this->getLevel()
                    );
                    $isOk = false;
                }
                break;
            case self::PERMISSION_WRITE:
                if (!$this->checkWritable($filePath)) {
                    $retValue[] = new CheckOutcome(
                        'check.file_permission.notwritable',
                        array('%0%' => $filePath),
                        $this->getLevel()
                    );
                    $isOk = false;
                }
                break;
            case self::PERMISSION_EXECUTE:
                if (!$this->checkExecutable($filePath)) {
                    $retValue[] = new CheckOutcome(
                        'check.file_permission.notexecutable',
                        array('%0%' => $filePath),
                        $this->getLevel()
                    );
                    $isOk = false;
                }
                break;
            default:
                $retValue[] = new CheckOutcome(
                    'check.file_permission.invalidtype',
                    array('%0%' => $permission),
                    CheckOutcome::ERROR
                );
                $isOk = false;
                break;
        }

        return $isOk;
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    private function checkFileExists($filePath)
    {
        return file_exists($filePath);
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    private function checkReadable($filePath)
    {
        return is_readable($filePath);
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    private function checkWritable($filePath)
    {
        return is_writable($filePath);
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    private function checkExecutable($filePath)
    {
        return is_executable($filePath);
    }
}
