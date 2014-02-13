<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

return array(
    'zf-configuration' => array(
        'config-file' => 'config/autoload/development.php',
    ),
    'zf-api-problem' => array(
        'render_error_controllers' => array(
            'ZF\Configuration\ConfigController',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'ZF\Configuration\ConfigController' => 'Json',
        ),
        'accept-whitelist' => array(
            'ZF\Configuration\ConfigController' => array(
                'application/json',
                'application/vnd.zfcampus.v1.config+json',
            ),
        ),
        'content-type-whitelist' => array(
            'ZF\Configuration\ConfigController' => array(
                'application/json',
                'application/vnd.zfcampus.v1.config+json',
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'ZF\Configuration\ConfigWriter' => 'Zend\Config\Writer\PhpArray',
        ),
    ),
);
