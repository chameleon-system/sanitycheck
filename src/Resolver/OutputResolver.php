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

class OutputResolver implements OutputResolverInterface
{
    /**
     * @var CheckOutputInterface[]
     */
    private $outputs = array();

    /**
     * {@inheritdoc}
     */
    public function get($alias)
    {
        if (!isset($this->outputs[$alias])) {
            throw new OutputNotFoundException("Requested non-existing output method: $alias");
        }

        return $this->outputs[$alias];
    }

    /**
     * {@inheritdoc}
     */
    public function addOutput($alias, CheckOutputInterface $output)
    {
        $this->outputs[$alias] = $output;
    }
}
