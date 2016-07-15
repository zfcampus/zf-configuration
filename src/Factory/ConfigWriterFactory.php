<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Configuration\Factory;

use Interop\Container\ContainerInterface;
use Zend\Config\Writer\PhpArray;

class ConfigWriterFactory
{
    /**
     * Create and return a PhpArray config writer.
     *
     * @param ContainerInterface $container
     * @return PhpArray
     */
    public function __invoke(ContainerInterface $container)
    {
        $writer = new PhpArray();

        if ($this->discoverEnableShortArrayFlag($container)) {
            $writer->setUseBracketArraySyntax(true);
        }

        return $writer;
    }

    /**
     * Discover the enable_short_array flag from configuration.
     *
     * @param ContainerInterface $container
     * @return bool
     */
    private function discoverEnableShortArrayFlag(ContainerInterface $container)
    {
        if (! $container->has('config')) {
            return false;
        }

        $config = $container->get('config');

        return (bool) $config['zf-configuration']['enable_short_array'];
    }
}
