<?php
namespace Application\Model\Resource;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class QueryCount implements InputFilterAwareInterface
{
    public $id;
    public $name;
    public $which_db;
    public $query;
    public $fail_count;
    public $last_checked;
    public $last_result;
    public $last_error;
    protected $inputFilter;
    public $lastException;

    public function exchangeArray($data)
    {
        $this->id               = (!empty($data['id'])) ? $data['id'] : null;
        $this->name             = (!empty($data['name'])) ? $data['name'] : null;
        $this->which_db         = (!empty($data['which_db'])) ? $data['which_db'] : null;
        $this->query            = (!empty($data['query'])) ? $data['query'] : null;
        $this->fail_count       = (!empty($data['fail_count'])) ? $data['fail_count'] : null;
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
                'name'     => 'which_db',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'query',
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
                            'max'      => 2000,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'fail_count',
                'required' => true,
                'validators' => array(
                    array(
                        'name'    => 'GreaterThan',
                        'options' => array(
                            'min'      => -1,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
    public function test($dbAdapter)
    {
        $this->last_result = true;
        $this->last_error = null;
        $this->last_checked = new \DateTime();
        try
        {
            if (is_null($dbAdapter)) {
                throw new \Exception('Database adapter is null');
            }
            $count = $dbAdapter->query($this->query)->execute()->count();
            if ($count >= $this->fail_count)
            {
                $this->last_error = 'Query results (' . $count . ') failed the count threshold (' . $this->fail_count . ')';
                $this->last_result = false;
            }
        } catch (\Exception $e) {
            $this->last_error = $e->getMessage();
            $this->last_result = false;
        }
    }
}