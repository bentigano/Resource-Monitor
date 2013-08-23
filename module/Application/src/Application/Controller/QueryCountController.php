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
use Application\Model\Resource\QueryCount;
use Application\Model\ResourceType;

class QueryCountController extends AbstractActionController
{
    protected $queryCountTable;
    protected $statusCheckTable;
    protected $resourceTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'queryCounts' => $this->getQueryCountTable()->fetchAll(),
        ));
    }
    
    public function addAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\QueryCountForm');
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $queryCount = new QueryCount();
            $form->setInputFilter($queryCount->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $queryCount->exchangeArray($form->getData());
                $this->getQueryCountTable()->saveQueryCount($queryCount);

                // Redirect to list of query counts
                return $this->redirect()->toRoute('queries');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('queries', array(
                'action' => 'add'
            ));
        }

        // Get the QueryCount with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $queryCount = $this->getQueryCountTable()->getQueryCount($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('queries', array(
                'action' => 'index'
            ));
        }

        $form = $this->getServiceLocator()->get('Application\Form\QueryCountForm');
        $form->bind($queryCount);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($queryCount->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getQueryCountTable()->saveQueryCount($queryCount);

                // Redirect to list of query counts
                return $this->redirect()->toRoute('queries');
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
            return $this->redirect()->toRoute('queries');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getQueryCountTable()->deleteQueryCount($id);
            }

            // Redirect to list of query counts
            return $this->redirect()->toRoute('queries');
        }

        return array(
            'id'    => $id,
            'queryCount' => $this->getQueryCountTable()->getQueryCount($id)
        );
    }
    
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('queries');
        }
        
        $resource_id = $this->getResourceTable()->getResourceByType(ResourceType::QUERY, $id)->id;
        
        return array(
            'id'    => $id,
            'queryCount' => $this->getQueryCountTable()->getQueryCount($id),
            'statusChecks' => $this->getStatusCheckTable()->fetchAll($resource_id),
        );
    }
    
    public function getQueryCountTable()
    {
        if (!$this->queryCountTable) {
            $sm = $this->getServiceLocator();
            $this->queryCountTable = $sm->get('Application\Model\Resource\QueryCountTable');
        }
        return $this->queryCountTable;
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
