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

class IndexController extends AbstractActionController
{
    protected $dbConnectionTable;
    protected $queryCountTable;
    protected $soapServiceTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'dbConnections' => $this->getDbConnectionTable()->fetchAll(),
            'queryCounts' => $this->getQueryCountTable()->fetchAll(),
            'soapServices' => $this->getSoapServiceTable()->fetchAll(),
        ));
        //return new ViewModel();
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
