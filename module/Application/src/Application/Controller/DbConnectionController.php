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
use Application\Model\Resource\DbConnection;
use Application\Model\ResourceType;

class DbConnectionController extends AbstractActionController
{
    protected $dbConnectionTable;
    protected $statusCheckTable;
    protected $resourceTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'dbConnections' => $this->getDbConnectionTable()->fetchAll(),
        ));
    }
    
    public function addAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\DbConnectionForm');
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $dbConnection = new DbConnection();
            $form->setInputFilter($dbConnection->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $dbConnection->exchangeArray($form->getData());
                $this->getDbConnectionTable()->saveDbConnection($dbConnection);

                // Redirect to list of db connections
                return $this->redirect()->toRoute('dbconnections');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dbconnections', array(
                'action' => 'add'
            ));
        }

        // Get the DbConnection with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $dbConnection = $this->getDbConnectionTable()->getDbConnection($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('dbconnections', array(
                'action' => 'index'
            ));
        }

        $form = $this->getServiceLocator()->get('Application\Form\DbConnectionForm');
        $form->bind($dbConnection);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($dbConnection->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getDbConnectionTable()->saveDbConnection($dbConnection);

                // Redirect to list of db connections
                return $this->redirect()->toRoute('dbconnections');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dbconnections');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getDbConnectionTable()->deleteDbConnection($id);
            }

            // Redirect to list of db connections
            return $this->redirect()->toRoute('dbconnections');
        }

        return array(
            'id'    => $id,
            'dbConnection' => $this->getDbConnectionTable()->getDbConnection($id)
        );
    }
    
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dbconnections');
        }
        
        $resource_id = $this->getResourceTable()->getResourceByType(ResourceType::DB, $id)->id;
        
        return array(
            'id'    => $id,
            'dbConnection' => $this->getDbConnectionTable()->getDbConnection($id),
            'statusChecks' => $this->getStatusCheckTable()->fetchAll($resource_id),
        );
    }
    
    public function getDbConnectionTable()
    {
        if (!$this->dbConnectionTable) {
            $sm = $this->getServiceLocator();
            $this->dbConnectionTable = $sm->get('Application\Model\Resource\DbConnectionTable');
        }
        return $this->dbConnectionTable;
    }
    
    public function getStatusCheckTable()
    {
        if (!$this->statusCheckTable) {
            $sm = $this->getServiceLocator();
            $this->statusCheckTable = $sm->get('Application\Model\StatusCheckTable');
        }
        return $this->statusCheckTable;
    }
    
    public function getResourceTable()
    {
        if (!$this->resourceTable) {
            $sm = $this->getServiceLocator();
            $this->resourceTable = $sm->get('Application\Model\Resource\ResourceTable');
        }
        return $this->resourceTable;
    }
}
