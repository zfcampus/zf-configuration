<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014-2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Configuration\Factory;

use Interop\Container\ContainerInterface;
use ZF\Configuration\ConfigWriter;
use ZF\Configuration\ModuleUtils;
use ZF\Configuration\ResourceFactory;

class ResourceFactoryFactory
{
    /**
     * @param ContainerInterface $container
     * @return ResourceFactory
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ResourceFactory(
            $container->get(ModuleUtils::class),
            $container->get(ConfigWriter::class)
        );
    }
}
