<?php
/**
 * Supervisor-Control config
 */
return array(
    'controllers' => array(
        'invokables' => array(
            'SupervisorControl\Controller\Supervisor'  => 'SupervisorControl\Controller\SupervisorController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'supervisor' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/supervisor-control/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'SupervisorControl\Controller',
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
            'SupervisorClient\SupervisorClient' => 'SupervisorControl\Client\ClientServiceFactory',
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
