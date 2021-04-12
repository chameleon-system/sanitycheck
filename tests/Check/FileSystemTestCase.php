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

use PHPUnit\Framework\TestCase;

function disk_free_space($dir)
{
    return FileSystemTestCase::$diskFreeSpace;
}

function disk_total_space($dir)
{
    return FileSystemTestCase::$diskTotalSpace;
}

function file_exists($dir)
{
    return FileSystemTestCase::$fileExists[$dir];
}

function is_dir($dir)
{
    return FileSystemTestCase::$isDir;
}

function is_readable($file)
{
    return FileSystemTestCase::$isReadable[$file];
}

function is_writable($file)
{
    return FileSystemTestCase::$isWritable[$file];
}

function is_executable($file)
{
    return FileSystemTestCase::$isExecutable[$file];
}

class FileSystemTestCase extends TestCase
{
    public static $diskFreeSpace;
    public static $diskTotalSpace;
    public static $fileExists = array();
    public static $isDir;
    public static $isReadable = array();
    public static $isWritable = array();
    public static $isExecutable = array();

    protected function setUp(): void
    {
        parent::setUp();

        self::$diskFreeSpace = 0;
        self::$diskTotalSpace = 0;
        self::$fileExists = array();
        self::$isDir = false;
        self::$isReadable = array();
        self::$isWritable = array();
        self::$isExecutable = array();
    }
}
