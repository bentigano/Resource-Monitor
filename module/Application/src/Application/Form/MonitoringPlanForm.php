<?php
namespace Application\Form;

use Zend\Form\Form;
use Application\Model\ResourceType;
use Application\Model\FrequencyUnit;

class MonitoringPlanForm extends Form
{
    public function __construct($name = null, $sm)
    {
        $this->sm = $sm;
        
        // we want to ignore the name passed
        parent::__construct('plans');
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
            'name' => 'resource_id',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'resource_id',
                'class' => 'input-xlarge',
            ),
            'options' => array(
                'label' => 'Resource',
                'empty_option' => 'Please choose a resource...',
                'value_options' => $this->getAllResourceOptions(),
            ),
        ));
        $this->add(array(
            'name' => 'enabled',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'enabled',
            ),
            'options' => array(
                'label' => 'Enabled',
            ),
        ));
        $this->add(array(
            'name' => 'run_mondays',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'run_mondays',
            ),
            'options' => array(
                'label' => 'Monday',
            ),
        ));
        $this->add(array(
            'name' => 'run_tuesdays',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'run_tuesdays',
            ),
            'options' => array(
                'label' => 'Tuesday',
            ),
        ));
        $this->add(array(
            'name' => 'run_wednesdays',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'run_wednesdays',
            ),
            'options' => array(
                'label' => 'Wednesdays',
            ),
        ));
        $this->add(array(
            'name' => 'run_thursdays',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'run_thursdays',
            ),
            'options' => array(
                'label' => 'Thursday',
            ),
        ));
        $this->add(array(
            'name' => 'run_fridays',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'run_fridays',
            ),
            'options' => array(
                'label' => 'Friday',
            ),
        ));
        $this->add(array(
            'name' => 'run_saturdays',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'run_saturdays',
            ),
            'options' => array(
                'label' => 'Saturday',
            ),
        ));
        $this->add(array(
            'name' => 'run_sundays',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'run_sundays',
            ),
            'options' => array(
                'label' => 'Sunday',
            ),
        ));
        $this->add(array(
            'name' => 'frequency',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'frequency',
                'class' => 'input-mini',
            ),
            'options' => array(
                'label' => 'Frequency',
            ),
        ));
        $this->add(array(
            'name' => 'frequency_unit',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'frequency_unit',
                'class' => 'input-medium',
            ),
            'options' => array(
                'label' => 'Frequency Unit',
                'empty_option' => 'Choose...',
                'value_options' => array(
                    FrequencyUnit::MINUTE => 'Minute(s)',
                    FrequencyUnit::HOUR => 'Hour(s)',
                    FrequencyUnit::DAY => 'Day(s)',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'starting_at',
            'type' => 'Time',
            'attributes' => array(
                'id' => 'starting_at',
                'class' => 'input-small',
            ),
            'options' => array(
                'label' => 'Starting at',
            ),
        ));
        $this->add(array(
            'name' => 'ending_at',
            'type' => 'Time',
            'attributes' => array(
                'id' => 'ending_at',
                'class' => 'input-small',
            ),
            'options' => array(
                'label' => 'Ending at',
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
    
    public function getAllResourceOptions()
    {
        $resources = $this->sm->get('Application\Model\Resource\ResourceTable')->fetchAll();
        $dbConnections = $this->sm->get('Application\Model\Resource\DbConnectionTable');
        $queries = $this->sm->get('Application\Model\Resource\QueryCountTable');
        $soapServices = $this->sm->get('Application\Model\Resource\SoapServiceTable');

        $dbOptions = array();
        $dbResources = $this->sm->get('Application\Model\Resource\ResourceTable')->fetchDBConnections();
        foreach ($dbResources as $resource) {
            $resourceObj = $dbConnections->getDbConnection($resource->source_id);
            $dbOptions[$resource->id] = $resourceObj->name;
        }
        
        $queryOptions = array();
        $queryResources = $this->sm->get('Application\Model\Resource\ResourceTable')->fetchQueryCounts();
        foreach ($queryResources as $resource) {
            $resourceObj = $queries->getQueryCount($resource->source_id);
            $queryOptions[$resource->id] = $resourceObj->name;
        }
        
        $soapOptions = array();
        $soapResources = $this->sm->get('Application\Model\Resource\ResourceTable')->fetchSoapServices();
        foreach ($soapResources as $resource) {
            $resourceObj = $soapServices->getSoapService($resource->source_id);
            $soapOptions[$resource->id] = $resourceObj->name;
        }

        
        $dropdownOptions = array(
          'db-connections' => array(
             'label' => 'DB Connections',
             'options' => $dbOptions,
          ),
          'queries' => array(
             'label' => 'Query Counts',
             'options' => $queryOptions,
          ),
          'soap-services' => array(
             'label' => 'SOAP Services',
             'options' => $soapOptions,
          ),
         );
        return $dropdownOptions;
    }
}