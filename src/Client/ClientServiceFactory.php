<?php

/**
 * @copyright   (c) 2014, Vrok
 * @license     http://customlicense CustomLicense
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl\Client;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Creates an instance of the supervisor client.
 */
class ClientServiceFactory implements FactoryInterface
{
    /**
     * Creates a client instance.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SupervisorClient
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config  = $serviceLocator->get('Config');
        $options = new ClientOptions($config['supervisor_client']);
        $client  = new SupervisorClient(
            $options->getHostname(),
            $options->getPort(),
            $options->getTimeout(),
            $options->getUsername(),
            $options->getPassword()
        );

        return $client;
    }
}
