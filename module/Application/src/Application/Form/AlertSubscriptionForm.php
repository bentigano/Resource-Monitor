<?php
namespace Application\Form;

use Zend\Form\Form;

class AlertSubscriptionForm extends Form
{
    protected $sm;
    
    public function __construct($name = null, $sm)
    {
        $this->sm = $sm;
        
        // we want to ignore the name passed
        parent::__construct('alerts');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'monitoring_plan_id',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'monitoring_plan_id',
                'class' => 'input-xlarge',
            ),
            'options' => array(
                'label' => 'Monitoring Plan',
                'empty_option' => 'Please choose a plan...',
                'value_options' => $this->getAllMonitoringPlanOptions(),
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'Email',
            'attributes' => array(
                'id' => 'email',
                'class' => 'input-xlarge',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'id' => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'Go',
            ),
            'options' => array(
                'label' => ' ', // this will put the button on the next line
            ),
        ));
        
    }
    
    public function getAllMonitoringPlanOptions()
    {
        $dropdownOptions = array();
        $plans = $this->sm->get('Application\Model\MonitoringPlanTable')->fetchAll();
        foreach ($plans as $plan) {
            $dropdownOptions[$plan->id] = $plan->name;
        }
        return $dropdownOptions;
    }
}