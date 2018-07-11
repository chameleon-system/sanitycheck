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

class FileExistsCheckTest extends FileSystemTestCase
{
    /**
     * @var DiskSpaceCheck
     */
    protected $fileExistsCheck;

    public function testAbsoluteFileExists()
    {
        $file = '/many/dir/wow';
        self::$fileExists[$file] = true;
        $fileList = array($file);

        $this->fileExistsCheck = new FileExistsCheck(CheckOutcome::ERROR, $fileList);

        $actualOutcomeList = $this->fileExistsCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_exists.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testAbsoluteFileDoesNotExist()
    {
        $file = '/many/dir/wow';
        self::$fileExists[$file] = false;
        $fileList = array($file);

        $this->fileExistsCheck = new FileExistsCheck(CheckOutcome::ERROR, $fileList);

        $actualOutcomeList = $this->fileExistsCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_exists.notexists', array('%0%' => $file), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testFileWithBasePathExists()
    {
        $file = '/many/dir/wow';
        $basePath = '/such/base/path';
        self::$fileExists[$basePath.'/'.$file] = true;
        $fileList = array($file);

        $this->fileExistsCheck = new FileExistsCheck(CheckOutcome::ERROR, $fileList, $basePath);

        $actualOutcomeList = $this->fileExistsCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_exists.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testFileWithBasePathDoesNotExist()
    {
        $file = '/many/dir/wow';
        $basePath = '/such/base/path';
        self::$fileExists[$basePath.'/'.$file] = false;
        $fileList = array($file);

        $this->fileExistsCheck = new FileExistsCheck(CheckOutcome::ERROR, $fileList, $basePath);

        $actualOutcomeList = $this->fileExistsCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_exists.notexists', array('%0%' => $basePath.'/'.$file), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testMultipleFilesWithBasePathDoNotExist()
    {
        $file1 = '/many/dir/wow';
        $file2 = '/such/dir/';
        $file3 = '/very/dir';
        $basePath = '/such/base/path';
        self::$fileExists[$basePath.'/'.$file1] = false;
        self::$fileExists[$basePath.'/'.$file2] = true;
        self::$fileExists[$basePath.'/'.$file3] = false;
        $fileList = array($file1, $file2, $file3);

        $this->fileExistsCheck = new FileExistsCheck(CheckOutcome::ERROR, $fileList, $basePath);

        $actualOutcomeList = $this->fileExistsCheck->performCheck();

        $expectedOutcomeList = array(
            new CheckOutcome('check.file_exists.notexists', array('%0%' => $basePath.'/'.$file1), CheckOutcome::ERROR),
            new CheckOutcome('check.file_exists.notexists', array('%0%' => $basePath.'/'.$file3), CheckOutcome::ERROR),
        );

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }
}
