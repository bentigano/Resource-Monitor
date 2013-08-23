<?php
namespace Application\Form;

use Zend\Form\Form;

class DbConnectionForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('dbconnection');
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
            'name' => 'dsn',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'dsn',
                'class' => 'input-xxlarge',
            ),
            'options' => array(
                'label' => 'DSN',
            ),
        ));
        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'username',
            ),
            'options' => array(
                'label' => 'Username',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'password',
            ),
            'options' => array(
                'label' => 'Password',
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