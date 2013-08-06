<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
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
            'ZF\ConfigController\ConfigController' => array(
                'application/json',
                'application/vnd.zfcampus.v1.config+json',
            ),
        ),
        'content-type-whitelist' => array(
            'ZF\ConfigController\ConfigController' => array(
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
