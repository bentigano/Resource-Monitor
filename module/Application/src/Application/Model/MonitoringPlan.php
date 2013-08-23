<?php
namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class MonitoringPlan implements InputFilterAwareInterface
{
    public $id; // int
    public $resource_id; // int
    public $name; // varchar255
    public $enabled; // tinyint
    public $last_checked; // datetime
    public $run_mondays; // tinyint
    public $run_tuesdays; // tinyint
    public $run_wednesdays; // tinyint
    public $run_thursdays; // tinyint
    public $run_fridays; // tinyint
    public $run_saturdays; // tinyint
    public $run_sundays; // tinyint
    public $frequency; // int
    public $frequency_unit; // varchar50
    public $starting_at; // datetime
    public $ending_at; // datetime
    protected $inputFilter;

    public function exchangeArray($data)
    {
        
        $this->id               = (!empty($data['id'])) ? $data['id'] : null;
        $this->resource_id      = (!empty($data['resource_id'])) ? $data['resource_id'] : null;
        $this->name             = (!empty($data['name'])) ? $data['name'] : null;
        $this->enabled          = (!empty($data['enabled'])) ? $data['enabled'] : null;
        $this->last_checked     = (!empty($data['last_checked'])) ? $data['last_checked'] : null;
        $this->run_mondays      = (!empty($data['run_mondays'])) ? $data['run_mondays'] : null;
        $this->run_tuesdays     = (!empty($data['run_tuesdays'])) ? $data['run_tuesdays'] : null;
        $this->run_wednesdays   = (!empty($data['run_wednesdays'])) ? $data['run_wednesdays'] : null;
        $this->run_thursdays    = (!empty($data['run_thursdays'])) ? $data['run_thursdays'] : null;
        $this->run_fridays      = (!empty($data['run_fridays'])) ? $data['run_fridays'] : null;
        $this->run_saturdays    = (!empty($data['run_saturdays'])) ? $data['run_saturdays'] : null;
        $this->run_sundays      = (!empty($data['run_sundays'])) ? $data['run_sundays'] : null;
        $this->frequency        = (!empty($data['frequency'])) ? $data['frequency'] : null;
        $this->frequency_unit   = (!empty($data['frequency_unit'])) ? $data['frequency_unit'] : null;
        $this->starting_at      = (!empty($data['starting_at'])) ? $data['starting_at'] : null;
        $this->ending_at        = (!empty($data['ending_at'])) ? $data['ending_at'] : null;
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
                'name'     => 'frequency',
                'required' => true,
                'validators' => array(
                    array(
                        'name'    => 'GreaterThan',
                        'options' => array(
                            'min'      => 0,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}