<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Configuration;

use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ProphecyInterface;
use Zend\Config\Writer\WriterInterface;
use ZF\Configuration\ConfigResource;
use ZF\Configuration\ConfigWriter;
use ZF\Configuration\Factory\ConfigResourceFactory;

class ConfigResourceFactoryTest extends TestCase
{
    /**
     * @var ContainerInterface|ProphecyInterface
     */
    private $container;

    /**
     * @var ConfigResourceFactory
     */
    private $factory;

    /**
     * @var WriterInterface
     */
    private $writer;

    protected function setUp()
    {
        $this->writer = $this->prophesize(WriterInterface::class)->reveal();
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->container->get(ConfigWriter::class)->willReturn($this->writer);
        $this->factory = new ConfigResourceFactory();
    }

    public function testReturnsInstanceOfConfigResource()
    {
        $this->container->has('config')->willReturn(false);

        $factory = $this->factory;
        $configResource = $factory($this->container->reveal());

        $this->assertInstanceOf(ConfigResource::class, $configResource);
    }

    public function testDefaultAttributesValues()
    {
        $this->container->has('config')->willReturn(false);

        $factory = $this->factory;

        /** @var ConfigResource $configResource */
        $configResource = $factory($this->container->reveal());

        $this->assertAttributeSame([], 'config', $configResource);
        $this->assertAttributeSame('config/autoload/development.php', 'fileName', $configResource);
        $this->assertAttributeSame($this->writer, 'writer', $configResource);
    }

    public function testCustomConfigFileIsSet()
    {
        $configFile = uniqid('config_file');
        $config = [
            'zf-configuration' => [
                'config_file' => $configFile,
            ],
        ];

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);

        $factory = $this->factory;

        /** @var ConfigResource $configResource */
        $configResource = $factory($this->container->reveal());

        $this->assertAttributeSame($config, 'config', $configResource);
        $this->assertAttributeSame($configFile, 'fileName', $configResource);
    }

    public function testCustomConfigurationIsPassToConfigResource()
    {
        $config = [
            'custom-configuration' => [
                'foo' => 'bar',
            ],
        ];

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn($config);

        $factory = $this->factory;

        /** @var ConfigResource $configResource */
        $configResource = $factory($this->container->reveal());

        $this->assertAttributeSame($config, 'config', $configResource);
    }
}
