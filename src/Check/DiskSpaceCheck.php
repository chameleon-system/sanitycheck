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
 * DiskSpaceCheck checks if a certain amount of disk space is available. See the bundle documentation on how to configure this check.
 */
class DiskSpaceCheck extends AbstractCheck
{
    const STRATEGY_ABSOLUTE = 'absolute';
    const STRATEGY_PERCENTAGE = 'percentage';

    /**
     * @var array
     */
    private $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB');
    /**
     * @var string
     */
    private $directory;
    /**
     * @var array
     */
    private $thresholdData = array();

    /**
     * @param int    $level
     * @param string $directory
     * @param array  $thresholds
     */
    public function __construct($level, $directory, array $thresholds)
    {
        parent::__construct($level);
        $this->directory = $directory;

        foreach ($thresholds as $level => $threshold) {
            $thresholdAmount = $this->parseAmount($threshold);
            $thresholdStrategy = $this->retrieveStrategy($threshold);
            $this->thresholdData[$level] = array($thresholdAmount, $thresholdStrategy);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function performCheck()
    {
        $retValue = array();

        if (!file_exists($this->directory)) {
            $retValue[] = new CheckOutcome(
                'check.file_exists.notexists',
                array('%0%' => $this->directory),
                CheckOutcome::EXCEPTION
            );

            return $retValue;
        }
        if (!is_dir($this->directory)) {
            $retValue[] = new CheckOutcome(
                'check.disk_space.nodirectory',
                array('%0%' => $this->directory),
                CheckOutcome::EXCEPTION
            );

            return $retValue;
        }

        $retValue = $this->checkDiskSpace();

        return $retValue;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function retrieveStrategy($value)
    {
        return '%' === substr($value, -1) ? self::STRATEGY_PERCENTAGE : self::STRATEGY_ABSOLUTE;
    }

    /**
     * Checks the space left on the disk given by the configured directory.
     *
     * @return array
     */
    private function checkDiskSpace()
    {
        $retValue = array();
        $freeSpace = disk_free_space($this->directory);
        $totalSpace = disk_total_space($this->directory);
        if (0 === $totalSpace) {
            $retValue[] = new CheckOutcome(
                'check.disk_space.totalzero',
                array('%0%' => $this->directory),
                CheckOutcome::ERROR
            );

            return $retValue;
        }
        $percentage = 100 * ($freeSpace / $totalSpace);

        $maxLevel = 0;
        $maxStrategy = self::STRATEGY_ABSOLUTE;

        foreach ($this->thresholdData as $level => $data) {
            $threshold = $data[0];
            $strategy = $data[1];

            switch ($strategy) {
                case self::STRATEGY_ABSOLUTE:
                    if (($freeSpace < $threshold) && $level > $maxLevel) {
                        $maxLevel = $level;
                        $maxStrategy = self::STRATEGY_ABSOLUTE;
                    }
                    break;
                case self::STRATEGY_PERCENTAGE:
                    if (($percentage < $threshold) && $level > $maxLevel) {
                        $maxLevel = $level;
                        $maxStrategy = self::STRATEGY_PERCENTAGE;
                    }
                    break;
                default:
                    $retValue[] = new CheckOutcome(
                        'check.disk_space.invalid_strategy',
                        array('%0%' => $strategy),
                        CheckOutcome::ERROR
                    );
                    break;
            }
        }

        if ($maxLevel > 0) {
            $retValue[] = new CheckOutcome(
                'check.disk_space.lowspace', array(
                '%0%' => $this->directory,
                '%1%' => $this->formatSpaceLeft($freeSpace, $totalSpace, $maxStrategy),
            ), $maxLevel
            );
        } else {
            $retValue[] = new CheckOutcome('check.disk_space.ok', array(), CheckOutcome::OK);
        }

        return $retValue;
    }

    /**
     * @param float  $freeSpace
     * @param float  $totalSpace
     * @param string $strategy
     *
     * @return string
     */
    private function formatSpaceLeft($freeSpace, $totalSpace, $strategy)
    {
        switch ($strategy) {
            case self::STRATEGY_ABSOLUTE:
                return $this->formatBytes($freeSpace, 1).' / '.$this->formatBytes($totalSpace);
                break;
            case self::STRATEGY_PERCENTAGE:
                return round(100 * ($freeSpace / $totalSpace), 1).'%';
                break;
            default:
                return $freeSpace;
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function parseAmount($value)
    {
        if ('%' === substr($value, -1)) {
            return substr($value, 0, -1);
        }
        $number = 0;
        $suffix = '';
        $value = preg_replace('/\s+/', '', $value);
        sscanf($value, '%u%s', $number, $suffix);
        if ('' === $suffix) {
            return $value;
        }

        if (false !== ($pow = array_search($suffix, $this->units))) {
            $bytes = $number * (1 << (10 * $pow));

            return (string) $bytes;
        } else {
            throw new \InvalidArgumentException(
                'Invalid size value. One of ['.join(
                    ', ',
                    $this->units
                ).'] allowed (or just a numeric byte value)'
            );
        }
    }

    /**
     * @param float $bytes
     * @param int   $precision
     *
     * @return string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($this->units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).$this->units[$pow];
    }
}
