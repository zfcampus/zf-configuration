<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014-2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Configuration;

use Zend\Config\Writer\PhpArray;

/**
 * Zend Framework module
 */
class Module
{
    /**
     * Retrieve module configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array('factories' => array(
            'ZF\Configuration\ConfigWriter' => function ($services) {
                $useShortArray = false;
                if ($services->has('config')) {
                    $config = $services->get('config');
                    if (isset($config['zf-configuration']['enable_short_array'])) {
                        $useShortArray = (bool) $config['zf-configuration']['enable_short_array'];
                    }
                }

                $writer = new PhpArray();
                if ($useShortArray && version_compare(PHP_VERSION, '5.4.0', '>=')) {
                    $writer->setUseBracketArraySyntax(true);
                }

                return $writer;
            },
            'ZF\Configuration\ConfigResource' => function ($services) {
                $config = array();
                $file   = 'config/autoload/development.php';
                if ($services->has('config')) {
                    $config = $services->get('config');
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
                $modules = $services->get(ModuleUtils::class);
                $writer  = $services->get(ConfigWriter::class);

                return new ResourceFactory($modules, $writer);
            },
            'ZF\Configuration\ModuleUtils' => function ($services) {
                $modules = $services->get('ModuleManager');
                return new ModuleUtils($modules);
            },
        ));
    }
}
