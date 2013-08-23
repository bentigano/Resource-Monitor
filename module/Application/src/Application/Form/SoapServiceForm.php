<?php
namespace Application\Form;

use Zend\Form\Form;

class SoapServiceForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('soapservice');
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
            'name' => 'wsdl_location',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'wsdl_location',
                'class' => 'input-xxlarge',
            ),
            'options' => array(
                'label' => 'WSDL Location',
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
}