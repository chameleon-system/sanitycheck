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

use ChameleonSystem\SanityCheck\Helper\PhpVersionHelper;
use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;

/**
 * PhpRuntimeVersionCheck verifies that the used PHP version conforms to a given rule.
 * See the bundle documentation on how to configure this check.
 */
class PhpRuntimeVersionCheck extends AbstractCheck
{
    /**
     * @var string
     */
    protected $nameToCheck;
    /**
     * @var array
     */
    protected $versions = array();

    /**
     * @param int          $level
     * @param string|array $versions
     */
    public function __construct($level, $versions)
    {
        parent::__construct($level);

        $phpVersionHelper = new PhpVersionHelper();
        $this->versions = $phpVersionHelper->parseVersions($versions);
    }

    /**
     * {@inheritdoc}
     */
    public function performCheck()
    {
        $retValue = array();

        $actualVersion = strtolower(phpversion());

        foreach ($this->versions as $version) {
            if (!version_compare($actualVersion, $version[0], $version[1])) {
                $retValue[] = new CheckOutcome(
                    'check.php_version.invalidversion',
                    array('%0%' => $actualVersion),
                    $this->getLevel()
                );
                break;
            }
        }

        if (empty($retValue)) {
            $retValue[] = new CheckOutcome('check.php_version.ok', array(), CheckOutcome::OK);
        }

        return $retValue;
    }
}
