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

class DiskSpaceCheckTest extends FileSystemTestCase
{
    /**
     * @var DiskSpaceCheck
     */
    protected $diskSpaceCheck;

    private $dir;

    public function testSimplePercentageOk()
    {
        $this->diskSpaceCheck = new DiskSpaceCheck(CheckOutcome::ERROR, $this->dir, array(CheckOutcome::WARNING => '10%'));

        $actualOutcomeList = $this->diskSpaceCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.disk_space.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testSimpleAbsoluteOk()
    {
        $this->diskSpaceCheck = new DiskSpaceCheck(CheckOutcome::ERROR, $this->dir, array(CheckOutcome::WARNING => '1000'));

        $actualOutcomeList = $this->diskSpaceCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.disk_space.ok', array(), CheckOutcome::OK));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testSimplePercentageNotOk()
    {
        $this->diskSpaceCheck = new DiskSpaceCheck(CheckOutcome::ERROR, $this->dir, array(CheckOutcome::WARNING => '50%'));

        $actualOutcomeList = $this->diskSpaceCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome(
            'check.disk_space.lowspace', array(
            '%0%' => $this->dir,
            '%1%' => '10%',
        ), CheckOutcome::WARNING
        ));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testSimpleAbsoluteNotOk()
    {
        self::$diskFreeSpace = 1024;

        $this->diskSpaceCheck = new DiskSpaceCheck(CheckOutcome::ERROR, $this->dir, array(CheckOutcome::WARNING => '2 KiB'));

        $actualOutcomeList = $this->diskSpaceCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome(
            'check.disk_space.lowspace', array(
            '%0%' => $this->dir,
            '%1%' => '1KiB / 9.77KiB',
        ), CheckOutcome::WARNING
        ));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testSimpleAbsoluteInvalidWarningThreshold()
    {
        $this->expectException('\InvalidArgumentException');
        $this->diskSpaceCheck = new DiskSpaceCheck(CheckOutcome::ERROR, $this->dir, array(CheckOutcome::WARNING => '2 KiBFoo'));
    }

    public function testSimpleDirDoesNotExist()
    {
        self::$fileExists[$this->dir] = false;

        $this->diskSpaceCheck = new DiskSpaceCheck(CheckOutcome::ERROR, $this->dir, array(CheckOutcome::WARNING => '5%'));

        $actualOutcomeList = $this->diskSpaceCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.file_exists.notexists', array('%0%' => $this->dir), CheckOutcome::EXCEPTION));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testSimpleIsNoDirectory()
    {
        self::$isDir = false;

        $this->diskSpaceCheck = new DiskSpaceCheck(CheckOutcome::ERROR, $this->dir, array(CheckOutcome::WARNING => '5%'));

        $actualOutcomeList = $this->diskSpaceCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.disk_space.nodirectory', array('%0%' => $this->dir), CheckOutcome::EXCEPTION));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testSimpleNoTotalSpace()
    {
        self::$diskFreeSpace = 0;
        self::$diskTotalSpace = 0;

        $this->diskSpaceCheck = new DiskSpaceCheck(CheckOutcome::ERROR, $this->dir, array(CheckOutcome::WARNING => '5%'));

        $actualOutcomeList = $this->diskSpaceCheck->performCheck();

        $expectedOutcomeList = array(new CheckOutcome('check.disk_space.totalzero', array('%0%' => $this->dir), CheckOutcome::ERROR));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testMultipleNotice()
    {
        $this->diskSpaceCheck = new DiskSpaceCheck(CheckOutcome::ERROR, $this->dir, array(
            CheckOutcome::NOTICE => '50%',
            CheckOutcome::WARNING => '10%',
            CheckOutcome::ERROR => '100',
        ));

        $actualOutcomeList = $this->diskSpaceCheck->performCheck();
        $expectedOutcomeList = array(new CheckOutcome('check.disk_space.lowspace', array(
            '%0%' => $this->dir,
            '%1%' => '10%', ), CheckOutcome::NOTICE));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    public function testMultipleWarning()
    {
        $this->diskSpaceCheck = new DiskSpaceCheck(CheckOutcome::ERROR, $this->dir, array(
            CheckOutcome::NOTICE => '50%',
            CheckOutcome::WARNING => '20%',
            CheckOutcome::ERROR => '100',
        ));

        $actualOutcomeList = $this->diskSpaceCheck->performCheck();
        $expectedOutcomeList = array(new CheckOutcome('check.disk_space.lowspace', array(
            '%0%' => $this->dir,
            '%1%' => '10%', ), CheckOutcome::WARNING));

        $this->assertEquals($expectedOutcomeList, $actualOutcomeList);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = '/many/dir/wow';

        self::$diskFreeSpace = 1000;
        self::$diskTotalSpace = 10000;
        self::$fileExists[$this->dir] = true;
        self::$isDir = true;
    }
}
