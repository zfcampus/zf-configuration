<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Configuration;

use Zend\Config\Writer\PhpArray;

/**
 * ZF2 module
 */
class Module
{
    /**
     * Retrieve autoloader configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array('Zend\Loader\StandardAutoloader' => array('namespaces' => array(
            __NAMESPACE__ => __DIR__ . '/src/ZF/Configuration/',
        )));
    }

    /**
     * Retrieve module configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array('factories' => array(
            'ZF\Configuration\ConfigWriter' => function ($services) {
                $useShortArray = false;
                if ($services->has('Config')) {
                    $config = $services->get('Config');
                    if (isset($config['zf-configuration']['enable_short_array'])) {
                        $useShortArray = (bool) $config['zf-configuration']['enable_short_array'];
                    }
                }
                $writer = new PhpArray();
                return $writer;
            },
            'ZF\Configuration\ConfigResource' => function ($services) {
                $config = array();
                $file   = 'config/autoload/development.php';
                if ($services->has('Config')) {
                    $config = $services->get('Config');
                    if (isset($config['zf-configuration'])
                        && isset($config['zf-configuration']['config_file'])
                    ) {
                        $file = $config['zf-configuration']['config_file'];
                    }
                }

                $writer = $services->get('ZF\Configuration\ConfigWriter');

                return new ConfigResource($config, $file, $writer);
            },
            'ZF\Configuration\ConfigResourceFactory' => function ($services) {
                $modules = $services->get('ZF\Configuration\ModuleUtils');
                $writer  = $services->get('ZF\Configuration\ConfigWriter');

                return new ResourceFactory($modules, $writer);
            },
            'ZF\Configuration\ModuleUtils' => function ($services) {
                $modules = $services->get('ModuleManager');
                return new ModuleUtils($modules);
            },
        ));
    }
}
