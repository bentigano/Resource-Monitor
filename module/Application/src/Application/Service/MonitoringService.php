<?php
namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
use Application\Model\Resource\DbConnection;
use Application\Model\Resource\DbConnectionTable;
use Application\Model\Resource\QueryCount;
use Application\Model\Resource\QueryCountTable;
use Application\Model\Resource\SoapService;
use Application\Model\Resource\SoapServiceTable;
use Application\Model\Resource\Resource;
use Application\Model\Resource\ResourceTable;
use Application\Model\MonitoringPlan;
use Application\Model\MonitoringPlanTable;
use Application\Model\StatusCheck;
use Application\Model\StatusCheckTable;
use Application\Model\ResourceType;
use Application\Model\FrequencyUnit;

class MonitoringService
{
    protected $sm;
    
    protected $monitoringPlanTable;
    protected $statusCheckTable;
    protected $resourceTable;
    protected $dbConnectionTable;
    protected $queryCountTable;
    protected $soapServiceTable;
    protected $notificationService;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
    }
    
    public function runAll()
    {
        $monitoringPlans = $this->getMonitoringPlanTable()->fetchAll();
        foreach ($monitoringPlans as $monitoringPlan) {
            if ($monitoringPlan->enabled &&
                $this->todayIsEnabled($monitoringPlan) &&
                $this->timeIsInWindow($monitoringPlan) &&
                $this->checkSchedule($monitoringPlan)) {
                $this->runOne($monitoringPlan);
            }
        }
    }
    
    public function runOne($monitoringPlan)
    {
        $testFailed = false;
        $resourceInfo = $this->getResourceTable()->getResource($monitoringPlan->resource_id);
        switch ($resourceInfo->type)
        {
            case ResourceType::DB:
                $resource = $this->getDbConnectionTable()->getDbConnection($resourceInfo->source_id);
                $this->getDbConnectionTable()->testDbConnection($resource);
                break;
            case ResourceType::QUERY:
                $resource = $this->getQueryCountTable()->getQueryCount($resourceInfo->source_id);
                $dbAdapter = $this->getDbConnectionTable()->getDbConnection($resource->which_db)->getAdapter();
                $this->getQueryCountTable()->testQueryCount($resource, $dbAdapter);
                break;
            case ResourceType::SOAP:
                $resource = $this->getSoapServiceTable()->getSoapService($resourceInfo->source_id);
                $this->getSoapServiceTable()->testSoapService($resource);
                break;
        }
        
        if (!is_null($resource)){            
            $monitoringPlan->last_checked = new \DateTime();
            $this->getMonitoringPlanTable()->touchMonitoringPlan($monitoringPlan); // updates the last_checked
            
            // if last result failed, send failure alerts
            if (!$resource->last_result) {
                $this->getNotificationService()->sendFailureAlert($monitoringPlan, $resource);
            }
            
            // log the status check
            $statusCheck = new StatusCheck();
            $statusCheck->resource_id = $monitoringPlan->resource_id;
            $statusCheck->datetime_checked = $monitoringPlan->last_checked;
            $statusCheck->success = $resource->last_result;
            $statusCheck->error_details = $resource->last_error;
            $this->getStatusCheckTable()->addStatusCheck($statusCheck);
        }
    }
    
    private function todayIsEnabled($monitoringPlan)
    {
        $dayOfWeek = date( "w", time()); // 0 = sunday, 1 = monday, etc...
        
        switch ($dayOfWeek)
        {
            case 0:
                return $monitoringPlan->run_sundays;
            case 1:
                return $monitoringPlan->run_mondays;
            case 2:
                return $monitoringPlan->run_tuesdays;
            case 3:
                return $monitoringPlan->run_wednesdays;
            case 4:
                return $monitoringPlan->run_thursdays;
            case 5:
                return $monitoringPlan->run_fridays;
            case 6:
                return $monitoringPlan->run_saturdays;
        }
    }
    
    private function timeIsInWindow($monitoringPlan)
    {
        $currentTime = time();

        $startTimeSet = false;
        $endTimeSet = false;

        if(!empty($monitoringPlan->starting_at)) {
            $startTime = strtotime($monitoringPlan->starting_at);
            $startTimeSet = true;
        }
        
        if(!empty($monitoringPlan->ending_at)) {
            $endTime = strtotime($monitoringPlan->ending_at);
            $endTimeSet = true;
        }
        
        if ($startTimeSet) {
            if ($startTime > $currentTime) {
                return false;
            }
        }
        
        if ($endTimeSet) {
            if ($endTime < $currentTime) {
                return false;
            }
        }
        return true;
    }
    
    private function checkSchedule($monitoringPlan)
    {
        if (is_null($monitoringPlan->last_checked))
        {
            return true;
        }
        $currentTime = new \DateTime();
        $checkAgainAt = new \DateTime($monitoringPlan->last_checked);
        switch ($monitoringPlan->frequency_unit)
        {
            case FrequencyUnit::MINUTE:
                $checkAgainAt->add(new \DateInterval('PT' . $monitoringPlan->frequency . 'M'));
                break;
            case FrequencyUnit::HOUR:
                $checkAgainAt->add(new \DateInterval('PT' . $monitoringPlan->frequency . 'H'));
                break;
            case FrequencyUnit::DAY:
                $checkAgainAt->add(new \DateInterval('P' . $monitoringPlan->frequency . 'D'));
                break;
        }
        return $checkAgainAt <= $currentTime;
    }
    
    public function getMonitoringPlanTable()
    {
        if (!$this->monitoringPlanTable) {
            $this->monitoringPlanTable = $this->sm->get('Application\Model\MonitoringPlanTable');
        }
        return $this->monitoringPlanTable;
    }
    
    public function getStatusCheckTable()
    {
        if (!$this->statusCheckTable) {
            $this->statusCheckTable = $this->sm->get('Application\Model\StatusCheckTable');
        }
        return $this->statusCheckTable;
    }
    
    public function getDbConnectionTable()
    {
        if (!$this->dbConnectionTable) {
            $this->dbConnectionTable = $this->sm->get('Application\Model\Resource\DbConnectionTable');
        }
        return $this->dbConnectionTable;
    }
    
    public function getQueryCountTable()
    {
        if (!$this->queryCountTable) {
            $this->queryCountTable = $this->sm->get('Application\Model\Resource\QueryCountTable');
        }
        return $this->queryCountTable;
    }
    
    public function getSoapServiceTable()
    {
        if (!$this->soapServiceTable) {
            $this->soapServiceTable = $this->sm->get('Application\Model\Resource\SoapServiceTable');
        }
        return $this->soapServiceTable;
    }
    
    public function getResourceTable()
    {
        if (!$this->resourceTable) {
            $this->resourceTable = $this->sm->get('Application\Model\Resource\ResourceTable');
        }
        return $this->resourceTable;
    }
    
    public function getNotificationService()
    {
        if (!$this->notificationService) {
            $this->notificationService = $this->sm->get('Application\Service\NotificationService');
        }
        return $this->notificationService;
    }
}