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
use Application\Model\Resource\SoapService;
use Application\Model\ResourceType;

class SoapServiceController extends AbstractActionController
{
    protected $soapServiceTable;
    protected $statusCheckTable;
    protected $resourceTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'soapServices' => $this->getSoapServiceTable()->fetchAll(),
        ));
    }
    
    public function addAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\SoapServiceForm');
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $soapService = new SoapService();
            $form->setInputFilter($soapService->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $soapService->exchangeArray($form->getData());
                $this->getSoapServiceTable()->saveSoapService($soapService);

                // Redirect to list of soap services
                return $this->redirect()->toRoute('soapservices');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('soapservices', array(
                'action' => 'add'
            ));
        }

        // Get the SoapService with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $soapService = $this->getSoapServiceTable()->getSoapService($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('soapservices', array(
                'action' => 'index'
            ));
        }

        $form = $this->getServiceLocator()->get('Application\Form\SoapServiceForm');
        $form->bind($soapService);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($soapService->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getSoapServiceTable()->saveSoapService($soapService);

                // Redirect to list of db connections
                return $this->redirect()->toRoute('soapservices');
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
            return $this->redirect()->toRoute('soapservices');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getSoapServiceTable()->deleteSoapService($id);
            }

            // Redirect to list of SOAP services
            return $this->redirect()->toRoute('soapservices');
        }

        return array(
            'id'    => $id,
            'soapService' => $this->getSoapServiceTable()->getSoapService($id)
        );
    }
    
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('soapservices');
        }
        
        $resource_id = $this->getResourceTable()->getResourceByType(ResourceType::SOAP, $id)->id;
        
        return array(
            'id'    => $id,
            'soapService' => $this->getSoapServiceTable()->getSoapService($id),
            'statusChecks' => $this->getStatusCheckTable()->fetchAll($resource_id),
        );
    }
    
    public function getSoapServiceTable()
    {
        if (!$this->soapServiceTable) {
            $sm = $this->getServiceLocator();
            $this->soapServiceTable = $sm->get('Application\Model\Resource\SoapServiceTable');
        }
        return $this->soapServiceTable;
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
