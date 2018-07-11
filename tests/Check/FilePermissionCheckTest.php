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

class FilePermissionCheckTest extends FileSystemTestCase
{
    /**
     * @var FilePermissionCheck
     */
    protected $filePermissionCheck;

    public function testAbsoluteFileIsReadable()
    {
        $file = '/many/dir/wow';
        self::$fileExists[$file] = true;
        self::$isReadable[$file] = true;
        $fileList = array($file);
        $permissionList = array(FilePermissionCheck::PERMISSION_READ);

        $this->filePermissionCheck = new FilePermissionCheck(CheckOutcome::ERROR, $fileList, $permissionList);

        $actualOutcomeList = $this->filePermissionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_permission.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testAbsoluteFileIsNotReadable()
    {
        $file = '/many/dir/wow';
        self::$fileExists[$file] = true;
        self::$isReadable[$file] = false;
        $fileList = array($file);
        $permissionList = array(FilePermissionCheck::PERMISSION_READ);

        $this->filePermissionCheck = new FilePermissionCheck(CheckOutcome::ERROR, $fileList, $permissionList);

        $actualOutcomeList = $this->filePermissionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_permission.notreadable', array('%0%' => $file), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testAbsoluteFileIsWritable()
    {
        $file = '/many/dir/wow';
        self::$fileExists[$file] = true;
        self::$isWritable[$file] = true;
        $fileList = array($file);
        $permissionList = array(FilePermissionCheck::PERMISSION_WRITE);

        $this->filePermissionCheck = new FilePermissionCheck(CheckOutcome::ERROR, $fileList, $permissionList);

        $actualOutcomeList = $this->filePermissionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_permission.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testAbsoluteFileIsNotWritable()
    {
        $file = '/many/dir/wow';
        self::$fileExists[$file] = true;
        self::$isWritable[$file] = false;
        $fileList = array($file);
        $permissionList = array(FilePermissionCheck::PERMISSION_WRITE);

        $this->filePermissionCheck = new FilePermissionCheck(CheckOutcome::ERROR, $fileList, $permissionList);

        $actualOutcomeList = $this->filePermissionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_permission.notwritable', array('%0%' => $file), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testAbsoluteFileIsExecutable()
    {
        $file = '/many/dir/wow';
        self::$fileExists[$file] = true;
        self::$isExecutable[$file] = true;
        $fileList = array($file);
        $permissionList = array(FilePermissionCheck::PERMISSION_EXECUTE);

        $this->filePermissionCheck = new FilePermissionCheck(CheckOutcome::ERROR, $fileList, $permissionList);

        $actualOutcomeList = $this->filePermissionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_permission.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testAbsoluteFileIsNotExecutable()
    {
        $file = '/many/dir/wow';
        self::$fileExists[$file] = true;
        self::$isExecutable[$file] = false;
        $fileList = array($file);
        $permissionList = array(FilePermissionCheck::PERMISSION_EXECUTE);

        $this->filePermissionCheck = new FilePermissionCheck(CheckOutcome::ERROR, $fileList, $permissionList);

        $actualOutcomeList = $this->filePermissionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_permission.notexecutable', array('%0%' => $file), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testFileWithBasePathIsReadable()
    {
        $file = '/many/dir/wow';
        $basePath = '/such/base/path';
        self::$fileExists[$basePath.'/'.$file] = true;
        self::$isReadable[$basePath.'/'.$file] = true;
        $fileList = array($file);
        $permissionList = array(FilePermissionCheck::PERMISSION_READ);

        $this->filePermissionCheck = new FilePermissionCheck(CheckOutcome::ERROR, $fileList, $permissionList, $basePath);

        $actualOutcomeList = $this->filePermissionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_permission.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testMultipleFilesWithBasePathAreNotReadable()
    {
        $file1 = '/many/dir/wow';
        $file2 = '/such/dir/';
        $file3 = '/very/dir';
        $basePath = '/such/base/path';
        self::$fileExists[$basePath.'/'.$file1] = true;
        self::$fileExists[$basePath.'/'.$file2] = true;
        self::$fileExists[$basePath.'/'.$file3] = true;
        self::$isReadable[$basePath.'/'.$file1] = false;
        self::$isReadable[$basePath.'/'.$file2] = true;
        self::$isReadable[$basePath.'/'.$file3] = false;
        $fileList = array($file1, $file2, $file3);
        $permissionList = array(FilePermissionCheck::PERMISSION_READ);

        $this->filePermissionCheck = new FilePermissionCheck(CheckOutcome::ERROR, $fileList, $permissionList, $basePath);

        $actualOutcomeList = $this->filePermissionCheck->performCheck();

        $expectedOutcomeList = array(
            new CheckOutcome('check.file_permission.notreadable', array('%0%' => $basePath.'/'.$file1), CheckOutcome::ERROR),
            new CheckOutcome('check.file_permission.notreadable', array('%0%' => $basePath.'/'.$file3), CheckOutcome::ERROR),
        );

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testAbsoluteFileDoesNotExist()
    {
        $file = '/many/dir/wow';
        self::$fileExists[$file] = false;
        $fileList = array($file);
        $permissionList = array(FilePermissionCheck::PERMISSION_READ);

        $this->filePermissionCheck = new FilePermissionCheck(CheckOutcome::ERROR, $fileList, $permissionList);

        $actualOutcomeList = $this->filePermissionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_exists.notexists', array('%0%' => $file), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testPermissionDoesNotExist()
    {
        $file = '/many/dir/wow';
        self::$fileExists[$file] = true;
        $fileList = array($file);
        $permissionList = array('NO_SUCH_PERMISSION');

        $this->filePermissionCheck = new FilePermissionCheck(CheckOutcome::ERROR, $fileList, $permissionList);

        $actualOutcomeList = $this->filePermissionCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_permission.invalidtype', array('%0%' => $permissionList[0]), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }
}
