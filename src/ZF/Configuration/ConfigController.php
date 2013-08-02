<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

namespace ZF\Configuration;

use Zend\Mvc\Controller\AbstractActionController;
use ZF\ApiProblem\ApiProblem;

class ConfigController extends AbstractActionController
{
    protected $config;

    public function __construct(ConfigResource $config)
    {
        $this->config = $config;
    }
    
    public function processAction()
    {
        $request = $this->getRequest();
        switch ($request->getMethod()) {
            case $request::METHOD_GET:
                return $this->config->fetch();
            case $request::METHOD_PATCH:
                $params = $this->bodyParams();
                return $this->config->patch($params);
            default:
                return new ApiProblem(405, 'Only the methods GET and PATCH are allowed for this URI');
        }
    }
}
