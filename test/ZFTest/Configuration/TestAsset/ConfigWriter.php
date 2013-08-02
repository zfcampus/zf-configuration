<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

namespace ZFTest\Configuration\TestAsset;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Config\Writer\PhpArray as BaseWriter;

class ConfigWriter extends BaseWriter
{
    public $writtenConfig;

    public function toFile($filename, $config, $exclusiveLock = true)
    {
        $this->writtenConfig = $config;
    }
}
