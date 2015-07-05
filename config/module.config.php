<?php
/**
 * Supervisor-Control config
 */
return [
// <editor-fold defaultstate="collapsed" desc="console">
    'console' => [
        'router' => [
            'routes' => [
                'check-process' => [
                    'options' => [
                        'route' => 'check-process <name>',
                        'defaults' => [
                            'controller' => 'SupervisorControl\Controller\Console',
                            'action'     => 'check-process',
                        ],
                    ],
                ],
            ],
        ],
    ],
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="controllers">
    'controllers' => [
        'invokables' => [
            'SupervisorControl\Controller\Supervisor' => 'SupervisorControl\Controller\SupervisorController',
            'SupervisorControl\Controller\Console' => 'SupervisorControl\Controller\ConsoleController',
        ],
    ],
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="navigation">
    'navigation' => [
        'default' => [
            'administration' => [
                'label' => 'navigation.administration', // default label or none is rendered
                'uri' => '#', // we need either a route or an URI to avoid fatal error
                'order' => 1000,
                'pages' => [
                    'server' => [
                        'label' => 'navigation.administration.server', // default label or none is rendered
                        'uri' => '#', // we need a either a route or an URI to avoid fatal error
                        'order' => 1000,
                        'pages' => [
                            [
                                'label'     => 'navigation.supervisor',
                                'route'     => 'supervisor',
                                'resource'  => 'controller/SupervisorControl\Controller\Supervisor',
                                'privilege' => 'index',
                                'order'     => 1000,
                                'pages'     => [
                                    [
                                        'label'   => 'navigation.supervisor.restart',
                                        'route'   => 'supervisor/restart',
                                        'visible' => false,
                                    ],
                                    [
                                        'label'   => 'navigation.supervisor.stopAll',
                                        'route'   => 'supervisor/stopall',
                                        'visible' => false,
                                    ],
                                    [
                                        'label'   => 'navigation.supervisor.group',
                                        'route'   => 'supervisor/group',
                                        'visible' => false,
                                    ],
                                    [
                                        'label'   => 'navigation.supervisor.stopGroup',
                                        'route'   => 'supervisor/stopgroup',
                                        'visible' => false,
                                    ],
                                    [
                                        'label'   => 'navigation.supervisor.stopProcess',
                                        'route'   => 'supervisor/stopprocess',
                                        'visible' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="router">
    'router' => [
        'routes' => [
            'supervisor' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/supervisor-control/',
                    'defaults' => [
                        'controller' => 'SupervisorControl\Controller\Supervisor',
                        'action'    => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'restart' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => 'restart[/]',
                            'defaults' => [
                                'action' => 'restart',
                            ],
                        ],
                    ],
                    'startall' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => 'start-all[/]',
                            'defaults' => [
                                'action' => 'start-all',
                            ],
                        ],
                    ],
                    'stopall' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => 'stop-all[/]',
                            'defaults' => [
                                'action' => 'stop-all',
                            ],
                        ],
                    ],
                    'group' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'group/[:name][/]',
                            'constraints' => [
                                'name' => '[a-zA-Z0-9_-]+'
                            ],
                            'defaults' => [
                                'action' => 'group',
                            ],
                        ],
                    ],
                    'startgroup' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'start-group/[:name][/]',
                            'constraints' => [
                                'name' => '[a-zA-Z0-9_-]+'
                            ],
                            'defaults' => [
                                'action' => 'start-group',
                            ],
                        ],
                    ],
                    'stopgroup' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'stop-group/[:name][/]',
                            'constraints' => [
                                'name' => '[a-zA-Z0-9_-]+'
                                ],
                            'defaults' => [
                                'action' => 'stop-group',
                            ],
                        ],
                    ],
                    'startprocess' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'start-process/[:name][/]',
                            'constraints' => [
                                'name' => '[a-zA-Z0-9_-]+'
                            ],
                            'defaults' => [
                                'action' => 'start-process',
                            ],
                        ],
                    ],
                    'stopprocess' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'       => 'stop-process/[:name][/]',
                            'constraints' => [
                                'name' => '[a-zA-Z0-9_-]+'
                                ],
                            'defaults' => [
                                'action' => 'stop-process',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="service_manager">
    'service_manager' => [
        'factories' => [
            'SupervisorClient' => 'SupervisorControl\Client\ClientServiceFactory',
        ],
    ],
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="supervisor_client">
    'supervisor_client' => [
        'hostname' => '127.0.0.1',
        'port' => 9001,
    ],
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="view_manager">
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
// </editor-fold>
];
