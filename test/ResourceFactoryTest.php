<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Configuration;

use PHPUnit_Framework_TestCase as TestCase;
use ZF\Configuration\ModuleUtils;
use ZF\Configuration\ResourceFactory;

class ResourceFactoryTest extends TestCase
{
    protected $testWriter = null;
    protected $resourceFactory = null;

    public function setup()
    {
        $this->resourceFactory = new ResourceFactory(
            $this->getMock('ZF\Configuration\ModuleUtils', [], [], '', false),
            $this->testWriter = new TestAsset\ConfigWriter()
        );
    }

    public function testCreateConfigResource()
    {
        $resource = $this->resourceFactory->createConfigResource(['foo' => 'bar'], '/path/to/file.php');
        $this->assertInstanceOf('ZF\Configuration\ConfigResource', $resource);
        $this->assertEquals(['foo' => 'bar'], $resource->fetch(true));
        $resource->overWrite(['foo' => 'baz']);

        $this->assertEquals('/path/to/file.php', $this->testWriter->writtenFilename);
        $this->assertEquals(['foo' => 'baz'], $this->testWriter->writtenConfig);
    }
}
