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
use Application\Model\AlertSubscription;

class AlertSubscriptionController extends AbstractActionController
{
    protected $alertSubscriptionTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'alertSubscriptions' => $this->getAlertSubscriptionTable()->fetchAll(),
        ));
    }
    
    public function addAction()
    {
        $form = $this->getServiceLocator()->get('Application\Form\AlertSubscriptionForm');
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $alertSubscription = new AlertSubscription();
            $form->setInputFilter($alertSubscription->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $alertSubscription->exchangeArray($form->getData());
                $this->getAlertSubscriptionTable()->saveAlertSubscription($alertSubscription);

                // Redirect to list of alert subscriptions
                return $this->redirect()->toRoute('alerts');
            }
        }
        return array('form' => $form);
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('alerts', array(
                'action' => 'add'
            ));
        }

        // Get the AlertSubscription with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $alertSubscription = $this->getAlertSubscriptionTable()->getAlertSubscription($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('alerts', array(
                'action' => 'index'
            ));
        }

        $form = $this->getServiceLocator()->get('Application\Form\AlertSubscriptionForm');
        $form->bind($alertSubscription);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($alertSubscription->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAlertSubscriptionTable()->saveAlertSubscription($alertSubscription);

                // Redirect to list of alert subscriptions
                return $this->redirect()->toRoute('alerts');
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
            return $this->redirect()->toRoute('alerts');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlertSubscriptionTable()->deleteAlertSubscription($id);
            }

            // Redirect to list of alert subscriptions
            return $this->redirect()->toRoute('alerts');
        }

        return array(
            'id'    => $id,
            'alertSubscription' => $this->getAlertSubscriptionTable()->getAlertSubscription($id)
        );
    }
    
    public function getAlertSubscriptionTable()
    {
        if (!$this->alertSubscriptionTable) {
            $sm = $this->getServiceLocator();
            $this->alertSubscriptionTable = $sm->get('Application\Model\AlertSubscriptionTable');
        }
        return $this->alertSubscriptionTable;
    }
}
