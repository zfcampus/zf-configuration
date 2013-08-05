<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

namespace ZF\Configuration;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Request;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\View\ApiProblemModel;

class ConfigController extends AbstractActionController
{
    protected $config;

    public function __construct(ConfigResource $config)
    {
        $this->config = $config;
    }
    
    public function processAction()
    {
        $request  = $this->getRequest();
        $headers  = $request->getHeaders();
        $useTrees = $this->shouldUseTrees($headers);

        switch ($request->getMethod()) {
            case $request::METHOD_GET:
                return $this->config->fetch($useTrees);
            case $request::METHOD_PATCH:
                $params = $this->bodyParams();
                return $this->config->patch($params, $useTrees);
            default:
                return new ApiProblemModel(
                    new ApiProblem(405, 'Only the methods GET and PATCH are allowed for this URI')
                );
        }
    }

    protected function shouldUseTrees($headers)
    {
        if (!$headers->has('content-type')) {
            return false;
        }

        $header      = $headers->get('content-type');
        $value       = $header->getFieldValue();
        $value       = explode(';', $value, 2);
        $contentType = array_shift($value);
        $contentType = strtolower($contentType);

        if ($contentType === 'application/vnd.zfcampus.v1.config+json') {
            return true;
        }

        return false;
    }

    /**
     * Set the request object manually
     *
     * Provided for testing.
     * 
     * @param  Request $request 
     * @return self
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }
}
