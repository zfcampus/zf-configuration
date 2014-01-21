<?php

namespace ZFTest\Configuration;

use PHPUnit_Framework_TestCase as TestCase;
use ZF\Configuration\ModuleUtils;
use ZF\Configuration\ResourceFactory;

class ResourceFactoryTest extends TestCase
{
    /** @var TestAsset\ConfigWriter */
    protected $testWriter = null;
    /** @var ResourceFactory */
    protected $resourceFactory = null;

    public function setup()
    {
        //(ModuleUtils $modules, ConfigWriter $writer)
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
 