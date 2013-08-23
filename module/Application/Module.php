<?php

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;

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
use Application\Model\AlertSubscription;
use Application\Model\AlertSubscriptionTable;
use Application\Model\StatusCheck;
use Application\Model\StatusCheckTable;
use Application\Service\MonitoringService;
use Application\Service\NotificationService;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    // ModuleManager calls this on every module, on every request, so only lightweight tasks (like attaching listeners)
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    
    // ModuleManager calls this on every module, on every request, so only lightweight tasks (like attaching listeners)
    public function init(ModuleManager $moduleManager)
    {

    }

    // ModuleManager calls this and merges the returned array (or Traversable) into the main app config
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    // ModuleManager calls this and returns the array to Zend\Loader\AutoloaderFactory
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getViewHelperConfig()
    {
        return array(
            'services' => array(
                'boolResult' => new View\Helper\BoolResult(),
                'date' => new View\Helper\Date(),
                'time' => new View\Helper\Time(),
             ),
             'factories' => array(
                'planName' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\PlanName();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'dbConnectionName' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\DbConnectionName();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
                'resourceName' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\ResourceName();
                    $viewHelper->setServiceLocator($serviceLocator);
                    return $viewHelper;
                },
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Model\Resource\DbConnectionTable' =>  function($sm) {
                    $tableGateway = $sm->get('DbConnectionTableGateway');
                    $table = new DbConnectionTable($tableGateway);
                    return $table;
                },
                'DbConnectionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DbConnection());
                    return new TableGateway('db_connections', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\Resource\QueryCountTable' =>  function($sm) {
                    $tableGateway = $sm->get('QueryCountTableGateway');
                    $table = new QueryCountTable($tableGateway);
                    return $table;
                },
                'QueryCountTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new QueryCount());
                    return new TableGateway('queries', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\Resource\SoapServiceTable' =>  function($sm) {
                    $tableGateway = $sm->get('SoapServiceTableGateway');
                    $table = new SoapServiceTable($tableGateway);
                    return $table;
                },
                'SoapServiceTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SoapService());
                    return new TableGateway('soap_services', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\Resource\ResourceTable' =>  function($sm) {
                    $tableGateway = $sm->get('ResourceTableGateway');
                    $table = new ResourceTable($tableGateway);
                    return $table;
                },
                'ResourceTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Resource());
                    return new TableGateway('resources', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\StatusCheckTable' =>  function($sm) {
                    $tableGateway = $sm->get('StatusCheckTableGateway');
                    $table = new StatusCheckTable($tableGateway);
                    return $table;
                },
                'StatusCheckTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new StatusCheck());
                    return new TableGateway('status_checks', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\MonitoringPlanTable' =>  function($sm) {
                    $tableGateway = $sm->get('MonitoringPlanTableGateway');
                    $table = new MonitoringPlanTable($tableGateway);
                    return $table;
                },
                'MonitoringPlanTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new MonitoringPlan());
                    return new TableGateway('monitoring_plans', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Model\AlertSubscriptionTable' =>  function($sm) {
                    $tableGateway = $sm->get('AlertSubscriptionTableGateway');
                    $table = new AlertSubscriptionTable($tableGateway);
                    return $table;
                },
                'AlertSubscriptionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AlertSubscription());
                    return new TableGateway('alert_subscriptions', $dbAdapter, null, $resultSetPrototype);
                },
                'Application\Service\MonitoringService' => function ($sm) {
                    $service = new MonitoringService($sm);
                    return $service;
                },
                'Application\Service\NotificationService' => function ($sm) {
                    $service = new NotificationService($sm);
                    return $service;
                },
                'Application\Form\DbConnectionForm' => function ($sm) {
                    $form = new \Application\Form\DbConnectionForm(NULL);
                    return $form;
                },
                'Application\Form\QueryCountForm' => function ($sm) {
                    $form = new \Application\Form\QueryCountForm(NULL, $sm);
                    return $form;
                },
                'Application\Form\SoapServiceForm' => function ($sm) {
                    $form = new \Application\Form\SoapServiceForm(NULL);
                    return $form;
                },
                'Application\Form\MonitoringPlanForm' => function ($sm) {
                    $form = new \Application\Form\MonitoringPlanForm(NULL, $sm);
                    return $form;
                },
                'Application\Form\AlertSubscriptionForm' => function ($sm) {
                    $form = new \Application\Form\AlertSubscriptionForm(NULL, $sm);
                    return $form;
                },
            ),
        );
    }
}
