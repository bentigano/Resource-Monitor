<?php

namespace Application\View\Helper;
 
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;

class PlanName extends AbstractHelper
{
    protected $serviceLocator;
    
    public function __invoke($monitoringPlanId)
    {
        $plan = $this->serviceLocator->get('Application\Model\MonitoringPlanTable')->getMonitoringPlan($monitoringPlanId);
        return $plan->name;
    }
    
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}