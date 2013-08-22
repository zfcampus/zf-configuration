<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

namespace ZF\Configuration;

use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\View\ApiProblemModel;

class ModuleConfigController extends AbstractConfigController
{
    protected $configFactory;

    public function __construct(ResourceFactory $factory)
    {
        $this->configFactory = $factory;
    }

    public function getConfig()
    {
        $module = $this->params()->fromQuery('module', false);
        if (!$module) {
            return new ApiProblemModel(
                new ApiProblem(400, 'Missing module parameter')
            );
        }
        $config = $this->configFactory->factory($module);
        return $config;
    }
} 
