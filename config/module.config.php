<?php
/**
 * Supervisor-Control config
 */
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'check-process' => array(
                    'options' => array(
                        'route' => 'check-process <name>',
                        'defaults' => array(
                            'controller' => 'SupervisorControl\Controller\Console',
                            'action'     => 'check-process',
                        ),
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'SupervisorControl\Controller\Supervisor'  => 'SupervisorControl\Controller\SupervisorController',
            'SupervisorControl\Controller\Console'     => 'SupervisorControl\Controller\ConsoleController',
        ),
    ),

    'navigation' => array(
        'default' => array(
            'administration' => array(
                'label' => 'navigation.administration', // default label or none is rendered
                'uri'   => '#', // we need either a route or an URI to avoid fatal error
                'order' => 1000,
                'pages' => array(
                    'server' => array(
                        'label' => 'navigation.administration.server', // default label or none is rendered
                        'uri'   => '#', // we need a either a route or an URI to avoid fatal error
                        'order' => 1000,
                        'pages' => array(
                            array(
                                'label'     => 'navigation.supervisorControl',
                                'route'     => 'supervisor',
                                'resource'  => 'controller/SupervisorControl\Controller\Supervisor',
                                'privilege' => 'index',
                                'order'     => 1000,
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'router' => array(
        'routes' => array(
            'supervisor' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/supervisor-control/',
                    'defaults' => array(
                        'controller'    => 'SupervisorControl\Controller\Supervisor',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'restart' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => 'restart[/]',
                            'defaults' => array(
                                'action' => 'restart',
                            ),
                        ),
                    ),
                    'startall' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => 'start-all[/]',
                            'defaults' => array(
                                'action' => 'start-all',
                            ),
                        ),
                    ),
                    'stopall' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => 'stop-all[/]',
                            'defaults' => array(
                                'action' => 'stop-all',
                            ),
                        ),
                    ),
                    'group' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => 'group/[:name][/]',
                            'constraints' => array(
                                'name' => '[a-zA-Z0-9_-]+'
                            ),
                            'defaults' => array(
                                'action' => 'group',
                            ),
                        ),
                    ),
                    'startgroup' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => 'start-group/[:name][/]',
                            'constraints' => array(
                                'name' => '[a-zA-Z0-9_-]+'
                            ),
                            'defaults' => array(
                                'action' => 'start-group',
                            ),
                        ),
                    ),
                    'stopgroup' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => 'stop-group/[:name][/]',
                            'constraints' => array(
                                'name' => '[a-zA-Z0-9_-]+'
                            ),
                            'defaults' => array(
                                'action' => 'stop-group',
                            ),
                        ),
                    ),
                    'startprocess' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => 'start-process/[:name][/]',
                            'constraints' => array(
                                'name' => '[a-zA-Z0-9_-]+'
                            ),
                            'defaults' => array(
                                'action' => 'start-process',
                            ),
                        ),
                    ),
                    'stopprocess' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => 'stop-process/[:name][/]',
                            'constraints' => array(
                                'name' => '[a-zA-Z0-9_-]+'
                            ),
                            'defaults' => array(
                                'action' => 'stop-process',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'SupervisorClient' => 'SupervisorControl\Client\ClientServiceFactory',
        ),
    ),

    // configured in supervisord.conf [inet_http_server] or [unix_http_server]
    'supervisor_client' => array(
        'hostname' => '127.0.0.1',
        'port'     => 9001,
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
