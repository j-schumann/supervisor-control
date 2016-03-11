<?php

/**
 * @copyright   (c) 2014, Vrok
 * @license     http://customlicense CustomLicense
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;

/**
 * Module bootstrapping.
 */
class Module implements
    ConfigProviderInterface,
    ControllerProviderInterface
{
    /**
     * Returns the modules default configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__.'/../config/module.config.php';
    }

    /**
     * Return additional serviceManager config with closures that should not be
     * in the config files to allow caching of the complete configuration.
     *
     * @return array
     * @todo alle controller auf ihre dependencies prüfen und ggf direct injecten
     */
    public function getControllerConfig()
    {
        return [
            'factories' => [
                'SupervisorControl\Controller\Supervisor' => function ($sm) {
                    $client = $sm->getServiceLocator()->get('SupervisorControl\Client\SupervisorClient');
                    return new Controller\SupervisorController($client);
                },
                'SupervisorControl\Controller\Console' => function ($sm) {
                    $client = $sm->getServiceLocator()->get('SupervisorControl\Client\SupervisorClient');
                    return new Controller\ConsoleController($client);
                },
            ],
        ];
    }
}
