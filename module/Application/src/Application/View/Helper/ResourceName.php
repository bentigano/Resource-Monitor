<?php

namespace Application\View\Helper;
 
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;
use Application\Model\ResourceType;

class ResourceName extends AbstractHelper
{
    protected $serviceLocator;
    
    public function __invoke($resourceId)
    {
        $resourceInfo = $this->serviceLocator->get('Application\Model\Resource\ResourceTable')->getResource($resourceId);
        switch ($resourceInfo->type) {
            case ResourceType::DB:
                $prefix = 'DB: ';
                $resource = $this->serviceLocator->get('Application\Model\Resource\DbConnectionTable')->getDbConnection($resourceInfo->source_id);
                break;
            case ResourceType::QUERY:
                $prefix = 'Query: ';
                $resource = $this->serviceLocator->get('Application\Model\Resource\QueryCountTable')->getQueryCount($resourceInfo->source_id);
                break;
            case ResourceType::SOAP:
                $prefix = 'SOAP: ';
                $resource = $this->serviceLocator->get('Application\Model\Resource\SoapServiceTable')->getSoapService($resourceInfo->source_id);
                break;
        }
        return $prefix . $resource->name;
    }
    
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}