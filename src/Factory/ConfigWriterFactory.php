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

        if ($this->discoverConfigFlag($container, 'enable_short_array')) {
            $writer->setUseBracketArraySyntax(true);
        }

        if ($this->discoverConfigFlag($container, 'class_name_scalars')) {
            $writer->setUseClassNameScalars(true);
        }

        return $writer;
    }

    /**
     * Discover the $key flag from configuration, if present.
     *
     * @param ContainerInterface $container
     * @param string $key
     * @return bool
     */
    private function discoverConfigFlag(ContainerInterface $container, $key)
    {
        if (! $container->has('config')) {
            return false;
        }

        $config = $container->get('config');

        if (! isset($config['zf-configuration'][$key])) {
            return false;
        }

        return (bool) $config['zf-configuration'][$key];
    }
}
