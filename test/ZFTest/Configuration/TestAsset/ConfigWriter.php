<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Configuration\TestAsset;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Config\Writer\PhpArray as BaseWriter;

class ConfigWriter extends BaseWriter
{
    public $writtenFilename;
    public $writtenConfig;

    public function toFile($filename, $config, $exclusiveLock = true)
    {
        $this->writtenFilename = $filename;
        $this->writtenConfig = $config;
    }
}
