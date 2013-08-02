<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

return array(
    'zf-configuration' => array(
        'config-file' => 'config/autoload/development.php',
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'ZF\Configuration\ConfigController' => array(
                'Zend\View\Model\JsonModel' => array(
                    'application/json',
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'zf-api-first-admin' => array(
                'child_routes' => array(
                    'api' => array(
                        'child_routes' => array(
                            'config' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/config',
                                    'defaults' => array(
                                        'controller' => 'ZF\Configuration\ConfigController',
                                        'action'     => 'process',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'ZF\Configuration\ConfigWriter' => 'Zend\Config\Writer\PhpArray',
        ),
    ),
);
