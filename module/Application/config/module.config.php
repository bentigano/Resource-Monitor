<?php

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'soapservices' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/soapservices[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\SoapService',
                        'action'     => 'index',
                    ),
                )
            ),
            'dbconnections' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/dbconnections[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\DbConnection',
                        'action'     => 'index',
                    ),
                )
            ),
            'queries' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/queries[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\QueryCount',
                        'action'     => 'index',
                    ),
                )
            ),
            'cron' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/cron[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Cron',
                        'action'     => 'index',
                    ),
                )
            ),
            'plans' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/plans[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\MonitoringPlan',
                        'action'     => 'index',
                    ),
                )
            ),
            'alerts' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/alerts[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\AlertSubscription',
                        'action'     => 'index',
                    ),
                )
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\DbConnection' => 'Application\Controller\DbConnectionController',
            'Application\Controller\QueryCount' => 'Application\Controller\QueryCountController',
            'Application\Controller\SoapService' => 'Application\Controller\SoapServiceController',
            'Application\Controller\Cron' => 'Application\Controller\CronController',
            'Application\Controller\MonitoringPlan' => 'Application\Controller\MonitoringPlanController',
            'Application\Controller\AlertSubscription' => 'Application\Controller\AlertSubscriptionController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'home',
            ),
            array(
                'label' => 'Services',
                'route' => 'soapservices',
                'pages' => array(
                    array(
                        'label' => 'Add',
                        'route' => 'soapservices',
                        'action' => 'add',
                    ),
                    array(
                        'label' => 'View',
                        'route' => 'soapservices',
                        'action' => 'view',
                    ),
                    array(
                        'label' => 'Edit',
                        'route' => 'soapservices',
                        'action' => 'edit',
                    ),
                    array(
                        'label' => 'Delete',
                        'route' => 'soapservices',
                        'action' => 'delete',
                    ),
                ),
            ),
            array(
                'label' => 'DB Connections',
                'route' => 'dbconnections',
                'pages' => array(
                    array(
                        'label' => 'Add',
                        'route' => 'dbconnections',
                        'action' => 'add',
                    ),
                    array(
                        'label' => 'View',
                        'route' => 'dbconnections',
                        'action' => 'view',
                    ),
                    array(
                        'label' => 'Edit',
                        'route' => 'dbconnections',
                        'action' => 'edit',
                    ),
                    array(
                        'label' => 'Delete',
                        'route' => 'dbconnections',
                        'action' => 'delete',
                    ),
                ),
            ),
            array(
                'label' => 'Queries',
                'route' => 'queries',
                'pages' => array(
                    array(
                        'label' => 'Add',
                        'route' => 'queries',
                        'action' => 'add',
                    ),
                    array(
                        'label' => 'View',
                        'route' => 'queries',
                        'action' => 'view',
                    ),
                    array(
                        'label' => 'Edit',
                        'route' => 'queries',
                        'action' => 'edit',
                    ),
                    array(
                        'label' => 'Delete',
                        'route' => 'queries',
                        'action' => 'delete',
                    ),
                ),
            ),
            array(
                'label' => 'Monitoring Plans',
                'route' => 'plans',
                'pages' => array(
                    array(
                        'label' => 'Add',
                        'route' => 'plans',
                        'action' => 'add',
                    ),
                    array(
                        'label' => 'View',
                        'route' => 'plans',
                        'action' => 'view',
                    ),
                    array(
                        'label' => 'Edit',
                        'route' => 'plans',
                        'action' => 'edit',
                    ),
                    array(
                        'label' => 'Delete',
                        'route' => 'plans',
                        'action' => 'delete',
                    ),
                ),
            ),
            array(
                'label' => 'Alert Subscriptions',
                'route' => 'alerts',
                'pages' => array(
                    array(
                        'label' => 'Add',
                        'route' => 'alerts',
                        'action' => 'add',
                    ),
                    array(
                        'label' => 'View',
                        'route' => 'alerts',
                        'action' => 'view',
                    ),
                    array(
                        'label' => 'Edit',
                        'route' => 'alerts',
                        'action' => 'edit',
                    ),
                    array(
                        'label' => 'Delete',
                        'route' => 'alerts',
                        'action' => 'delete',
                    ),
                ),
            ),
        ),
    ),
);
