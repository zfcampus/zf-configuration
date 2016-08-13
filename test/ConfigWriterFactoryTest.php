<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Configuration;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Prophecy\ProphecyInterface;
use Zend\Config\Writer\PhpArray;
use ZF\Configuration\Factory\ConfigWriterFactory;

class ConfigWriterFactoryTest extends TestCase
{
    /**
     * @var ContainerInterface|ProphecyInterface
     */
    private $container;

    /**
     * @var ConfigWriterFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new ConfigWriterFactory();
    }

    public function testReturnsInstanceOfPhpArrayWriter()
    {
        $factory = $this->factory;
        $configWriter = $factory($this->container->reveal());

        $this->assertInstanceOf(PhpArray::class, $configWriter);
    }

    public function testDefaultFlagsValues()
    {
        $factory = $this->factory;

        /** @var PhpArray $configWriter */
        $configWriter = $factory($this->container->reveal());

        $this->assertAttributeSame(false, 'useBracketArraySyntax', $configWriter);
        $this->assertFalse($configWriter->getUseClassNameScalars());
    }

    public function testEnableShortArrayFlagIsSet()
    {
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([
            'zf-configuration' => [
                'enable_short_array' => true,
            ],
        ]);

        $factory = $this->factory;

        /** @var PhpArray $configWriter */
        $configWriter = $factory($this->container->reveal());

        $this->assertAttributeSame(true, 'useBracketArraySyntax', $configWriter);
    }

    public function testClassNameScalarsFlagIsSet()
    {
        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([
            'zf-configuration' => [
                'class_name_scalars' => true,
            ],
        ]);

        $factory = $this->factory;

        /** @var PhpArray $configWriter */
        $configWriter = $factory($this->container->reveal());

        $this->assertTrue($configWriter->getUseClassNameScalars());
    }
}
