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
use ChameleonSystem\SanityCheck\Output\CheckOutputInterface;

/**
 * CheckResolverInterface defines a service which can be used to discover outputs that
 * were previously registered through the service container.
 */
interface OutputResolverInterface
{
    /**
     * Finds an output which is identified by the given $alias.
     *
     * @param string $alias The alias as given in the chameleon_system.sanity_check.output tag
     *
     * @return CheckOutputInterface
     *
     * @throws OutputNotFoundException
     */
    public function get($alias);

    /**
     * Registers an output that can afterwards be discovered using an alias.
     *
     * @param $alias
     * @param CheckOutputInterface $output
     */
    public function addOutput($alias, CheckOutputInterface $output);
}
