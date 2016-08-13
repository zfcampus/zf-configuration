<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Configuration\Factory;

use Interop\Container\ContainerInterface;
use ZF\Configuration\ConfigResource;
use ZF\Configuration\ConfigWriter;

class ConfigResourceFactory
{
    /**
     * Default configuration file to use.
     * @param string
     */
    private $defaultConfigFile = 'config/autoload/development.php';

    /**
     * Create and return a ConfigResource.
     *
     * @param ContainerInterface $container
     * @return ConfigResource
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $this->fetchConfig($container);

        return new ConfigResource(
            $config,
            $this->discoverConfigFile($config),
            $container->get(ConfigWriter::class)
        );
    }

    /**
     * Fetch configuration from the container, if possible.
     *
     * @param ContainerInterface $container
     * @return array
     */
    private function fetchConfig(ContainerInterface $container)
    {
        if (! $container->has('config')) {
            return [];
        }

        return $container->get('config');
    }

    /**
     * Discover the configuration file to use.
     *
     * @param array $config
     * @return string
     */
    private function discoverConfigFile(array $config)
    {
        if (! isset($config['zf-configuration']['config_file'])) {
            return $this->defaultConfigFile;
        }

        return $config['zf-configuration']['config_file'];
    }
}
