<?php
namespace Application\Form;

use Zend\Form\Form;

class QueryCountForm extends Form
{
    protected $sm;
    
    public function __construct($name = null, $sm)
    {
        $this->sm = $sm;
        
        // we want to ignore the name passed
        parent::__construct('querycount');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'name',
                'class' => 'input-xlarge',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));
        $this->add(array(
            'name' => 'which_db',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'which_db',
                'class' => 'input-xlarge',
            ),
            'options' => array(
                'label' => 'DB Connection',
                'empty_option' => 'Please choose a DB Connection...',
                'value_options' => $this->getAllDbConnectionOptions(),
            ),
        ));
        $this->add(array(
            'name' => 'query',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'query',
                'class' => 'input-xxlarge',
                'rows' => 5,
            ),
            'options' => array(
                'label' => 'Query',
            ),
        ));
        $this->add(array(
            'name' => 'fail_count',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'fail_count',
                'class' => 'input-mini',
            ),
            'options' => array(
                'label' => 'Fail Count',
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
    
    public function getAllDbConnectionOptions()
    {
        $dropdownOptions = array();
        $plans = $this->sm->get('Application\Model\Resource\DbConnectionTable')->fetchAll();
        foreach ($plans as $plan) {
            $dropdownOptions[$plan->id] = $plan->name;
        }
        return $dropdownOptions;
    }
}