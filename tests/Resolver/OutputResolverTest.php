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

use ChameleonSystem\SanityCheck\Exception\OutputNotFoundException;
use ChameleonSystem\SanityCheck\Output\NullCheckOutput;
use PHPUnit\Framework\TestCase;

class OutputResolverTest extends TestCase
{
    /**
     * @var OutputResolver $outputResolver
     */
    protected $outputResolver;

    protected $data;
    protected $dataFlat;

    public function testGet()
    {
        $output = new NullCheckOutput();
        $this->outputResolver->addOutput('my_output', $output);
        $actual = $this->outputResolver->get('my_output');

        $this->assertEquals($output, $actual);
    }

    public function testGetNotFound()
    {
        try {
            $this->outputResolver->get('very_not_existing_output');
            $this->assertTrue(false);
        } catch (OutputNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    protected function setUp()
    {
        parent::setUp();
        $this->outputResolver = new OutputResolver();
    }
}
