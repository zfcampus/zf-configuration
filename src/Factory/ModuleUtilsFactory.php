<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014-2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Configuration\Factory;

use Interop\Container\ContainerInterface;
use ZF\Configuration\ModuleUtils;

class ModuleUtilsFactory
{
    /**
     * @param ContainerInterface $container
     * @return ModuleUtils
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ModuleUtils($container->get('ModuleManager'));
    }
}
