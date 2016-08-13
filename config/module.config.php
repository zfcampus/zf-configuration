<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014-2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Configuration;

return [
    'zf-configuration' => [
        'config_file' => 'config/autoload/development.php',
        // set the following flag if you wish to use short array syntax
        // in configuration files manipulated by the ConfigWriter:
        // 'enable_short_array' => true,

        // class_name_scalars defines whether configuration files
        // manipulated by the ConfigWriter should use ::class notation
        // 'class_name_scalars' => true,
    ],
    'service_manager' => [
        'factories' => [
            ConfigResource::class        => Factory\ConfigResourceFactory::class,
            ConfigResourceFactory::class => Factory\ResourceFactoryFactory::class,
            ConfigWriter::class          => Factory\ConfigWriterFactory::class,
            ModuleUtils::class           => Factory\ModuleUtilsFactory::class,
        ],
    ],
];
