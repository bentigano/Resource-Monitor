<?php

namespace Application\View\Helper;
 
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;

class DbConnectionName extends AbstractHelper
{
    protected $serviceLocator;
    
    public function __invoke($dbConnectionId)
    {
        $dbConnection = $this->serviceLocator->get('Application\Model\Resource\DbConnectionTable')->getDbConnection($dbConnectionId);
        return $dbConnection->name;
    }
    
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}