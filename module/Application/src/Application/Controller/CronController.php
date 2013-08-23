<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CronController extends AbstractActionController
{
    protected $dbConnectionTable;
    protected $queryCountTable;
    protected $soapServiceTable;
    
    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $monitoringService = $sm->get('Application\Service\MonitoringService');
        $monitoringService->runAll();

        $response = $this->getResponse();
        $response->setStatusCode(200);
        return $response;
    }
    
    public function testSoapAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        try {
            $soapService = $this->getSoapServiceTable()->getSoapService($id);
        }
        catch (\Exception $ex) { }
        $this->getSoapServiceTable()->testSoapService($soapService);
        
        // TODO: add a statusCheck with the soapService variable info (might need to GET it first)
        
        $response = $this->getResponse();
        $response->setStatusCode(200);
        return $response;
    }
    
    public function testDbConnectionAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        try {
            $dbConnection = $this->getDbConnectionTable()->getDbConnection($id);
        }
        catch (\Exception $ex) { }
        $this->getDbConnectionTable()->testDbConnection($dbConnection);
        
        // TODO: add a statusCheck with the dbConnection variable info (might need to GET it first)
        
        $response = $this->getResponse();
        $response->setStatusCode(200);
        return $response;
    }
    
    public function testQueryCountAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        try {
            $queryCount = $this->getQueryCountTable()->getQueryCount($id);
        }
        catch (\Exception $ex) { }
        
        try {
            $dbAdapter = $this->getDbConnectionTable()->getDbConnection($queryCount->which_db)->getAdapter();
        }
        catch (\Exception $ex) {
            
        }
        
        $this->getQueryCountTable()->testQueryCount($queryCount, $dbAdapter);
        
        // TODO: add a statusCheck with the queryCount variable info (might need to GET it first)
        
        $response = $this->getResponse();
        $response->setStatusCode(200);
        return $response;
    }
    
    public function getDbConnectionTable()
    {
        if (!$this->dbConnectionTable) {
            $sm = $this->getServiceLocator();
            $this->dbConnectionTable = $sm->get('Application\Model\Resource\DbConnectionTable');
        }
        return $this->dbConnectionTable;
    }
    
    public function getQueryCountTable()
    {
        if (!$this->queryCountTable) {
            $sm = $this->getServiceLocator();
            $this->queryCountTable = $sm->get('Application\Model\Resource\QueryCountTable');
        }
        return $this->queryCountTable;
    }
    
    public function getSoapServiceTable()
    {
        if (!$this->soapServiceTable) {
            $sm = $this->getServiceLocator();
            $this->soapServiceTable = $sm->get('Application\Model\Resource\SoapServiceTable');
        }
        return $this->soapServiceTable;
    }
}
