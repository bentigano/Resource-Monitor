<?php
namespace Application\Model\Resource;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class SoapService implements InputFilterAwareInterface
{
    public $id;
    public $name;
    public $wsdl_location;
    public $last_checked;
    public $last_result;
    public $last_error;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id               = (!empty($data['id'])) ? $data['id'] : null;
        $this->name             = (!empty($data['name'])) ? $data['name'] : null;
        $this->wsdl_location    = (!empty($data['wsdl_location'])) ? $data['wsdl_location'] : null;
        $this->last_checked     = (!empty($data['last_checked'])) ? $data['last_checked'] : null;
        $this->last_result      = $data['last_result'];
        $this->last_error       = (!empty($data['last_error'])) ? $data['last_error'] : null;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'wsdl_location',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
    public function test()
    {
        $this->last_result = true;
        $this->last_error = null;
        $this->last_checked = new \DateTime();
        try
        {
            $client = new \Zend\Soap\Client($this->wsdl_location);
            $client->getFunctions();
        } catch (\Exception $e) {
            $this->last_error = $e->getMessage();
            $this->last_result = false;
        }
    }
}