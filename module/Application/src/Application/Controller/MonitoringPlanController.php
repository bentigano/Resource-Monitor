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
use Application\Model\MonitoringPlan;
use Application\Model\ResourceType;

class MonitoringPlanController extends AbstractActionController
{
    protected $monitoringPlanTable;
    protected $statusCheckTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'monitoringPlans' => $this->getMonitoringPlanTable()->fetchAll(),
        ));
    }
    
    public function addAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\MonitoringPlanForm');
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $monitoringPlan = new MonitoringPlan();
            $form->setInputFilter($monitoringPlan->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $monitoringPlan->exchangeArray($form->getData());
                $this->getMonitoringPlanTable()->saveMonitoringPlan($monitoringPlan);

                // Redirect to list of monitoring plans
                return $this->redirect()->toRoute('plans');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('plans', array(
                'action' => 'add'
            ));
        }

        // Get the MonitoringPlan with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $monitoringPlan = $this->getMonitoringPlanTable()->getMonitoringPlan($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('plans', array(
                'action' => 'index'
            ));
        }

        $form = $this->getServiceLocator()->get('Application\Form\MonitoringPlanForm');
        $form->bind($monitoringPlan);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($monitoringPlan->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getMonitoringPlanTable()->saveMonitoringPlan($monitoringPlan);

                // Redirect to list of monitoring plans
                return $this->redirect()->toRoute('plans');
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
            return $this->redirect()->toRoute('plans');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getMonitoringPlanTable()->deleteMonitoringPlan($id);
            }

            // Redirect to list of monitoring plans
            return $this->redirect()->toRoute('plans');
        }

        return array(
            'id'    => $id,
            'monitoringPlan' => $this->getMonitoringPlanTable()->getMonitoringPlan($id)
        );
    }
    
    public function viewAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('plans');
        }
        
        $plan = $this->getMonitoringPlanTable()->getMonitoringPlan($id);
        
        return array(
            'id'    => $id,
            'monitoringPlan' => $plan,
            'statusChecks' => $this->getStatusCheckTable()->fetchAll($plan->resource_id),
        );
    }
    
    public function getMonitoringPlanTable()
    {
        if (!$this->monitoringPlanTable) {
            $sm = $this->getServiceLocator();
            $this->monitoringPlanTable = $sm->get('Application\Model\MonitoringPlanTable');
        }
        return $this->monitoringPlanTable;
    }
    
    public function getStatusCheckTable()
    {
        if (!$this->statusCheckTable) {
            $sm = $this->getServiceLocator();
            $this->statusCheckTable = $sm->get('Application\Model\StatusCheckTable');
        }
        return $this->statusCheckTable;
    }
}
