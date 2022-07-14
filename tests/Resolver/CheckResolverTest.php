<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheck\Resolver;

use ChameleonSystem\SanityCheck\Check\CheckInterface;
use PHPUnit\Framework\TestCase;

class CheckResolverTest extends TestCase
{
    /**
     * @var CheckResolver $checkResolver
     */
    protected $checkResolver;
    /**
     * @var CheckInterface $check1
     */
    protected $check1;
    /**
     * @var CheckInterface $check2
     */
    protected $check2;
    /**
     * @var CheckInterface $check3
     */
    protected $check3;

    public function testFindSingleOk()
    {
        $revealedCheck = $this->check1->reveal();
        $this->checkResolver->addCheck('chameleon_system_sanitycheck.test.check1', $revealedCheck);

        $checks = $this->checkResolver->findChecksForName('chameleon_system_sanitycheck.test.check1');

        $this->assertCount(1, $checks);
        $this->assertEquals($revealedCheck, $checks[0]);
    }

    public function testFindSingleNotFound()
    {
        $this->expectException('\ChameleonSystem\SanityCheck\Exception\CheckNotFoundException');
        $this->checkResolver->findChecksForName('chameleon_system_sanitycheck.test.check1');
    }

    public function testFindMultipleOk()
    {
        $revealedCheck1 = $this->check1->reveal();
        $this->checkResolver->addCheck('chameleon_system_sanitycheck.test.check1', $revealedCheck1);
        $revealedCheck2 = $this->check2->reveal();
        $this->checkResolver->addCheck('chameleon_system_sanitycheck.test.check2', $revealedCheck2);
        $revealedCheck3 = $this->check3->reveal();
        $this->checkResolver->addCheck('chameleon_system_sanitycheck.test.check3', $revealedCheck3);

        $checks = $this->checkResolver->findChecksForNameList(
            array(
                'chameleon_system_sanitycheck.test.check1',
                'chameleon_system_sanitycheck.test.check2',
                'chameleon_system_sanitycheck.test.check3',
            )
        );

        $this->assertCount(3, $checks);
        $this->assertEquals($revealedCheck1, $checks[0]);
        $this->assertEquals($revealedCheck2, $checks[1]);
        $this->assertEquals($revealedCheck3, $checks[2]);
    }

    public function testFindMultipleSomeNotFound()
    {
        $revealedCheck1 = $this->check1->reveal();
        $this->checkResolver->addCheck('chameleon_system_sanitycheck.test.check1', $revealedCheck1);
        $revealedCheck3 = $this->check3->reveal();
        $this->checkResolver->addCheck('chameleon_system_sanitycheck.test.check3', $revealedCheck3);

        $this->expectException('\ChameleonSystem\SanityCheck\Exception\CheckNotFoundException');
        $checks = $this->checkResolver->findChecksForNameList(
            array(
                'chameleon_system_sanitycheck.test.check1',
                'chameleon_system_sanitycheck.test.check2',
                'chameleon_system_sanitycheck.test.check3',
            )
        );
    }

    public function testFindMultipleNoneFound()
    {
        $this->expectException('\ChameleonSystem\SanityCheck\Exception\CheckNotFoundException');
        $this->checkResolver->findChecksForNameList(
            array(
                'chameleon_system_sanitycheck.test.such_check_very_absence',
                'chameleon_system_sanitycheck.test.many_check_no_find',
            )
        );
    }

    public function testFindAll()
    {
        $revealedCheck1 = $this->check1->reveal();
        $this->checkResolver->addCheck('chameleon_system_sanitycheck.test.check1', $revealedCheck1);
        $revealedCheck2 = $this->check2->reveal();
        $this->checkResolver->addCheck('chameleon_system_sanitycheck.test.check2', $revealedCheck2);
        $revealedCheck3 = $this->check3->reveal();
        $this->checkResolver->addCheck('chameleon_system_sanitycheck.test.check31', $revealedCheck3);

        $checks = $this->checkResolver->findAllChecks();

        $this->assertCount(3, $checks);
        $this->assertEquals($revealedCheck1, $checks[0]);
        $this->assertEquals($revealedCheck2, $checks[1]);
        $this->assertEquals($revealedCheck3, $checks[2]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->checkResolver = new CheckResolver();
        $this->check1 = $this->prophesize('ChameleonSystem\SanityCheck\Check\CheckInterface');
        $this->check2 = $this->prophesize('ChameleonSystem\SanityCheck\Check\CheckInterface');
        $this->check3 = $this->prophesize('ChameleonSystem\SanityCheck\Check\CheckInterface');
    }
}
