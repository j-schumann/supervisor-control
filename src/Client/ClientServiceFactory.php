<?php

/**
 * @copyright   (c) 2014-16, Vrok
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl\Client;

use Interop\Container\ContainerInterface;
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
     * @param ContainerInterface $container
     * @todo params doc
     *
     * @return SupervisorClient
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config  = $container->get('Config');
        $coptions = new ClientOptions($config['supervisor_client']);
        $client  = new SupervisorClient(
            $coptions->getHostname(),
            $coptions->getPort(),
            $coptions->getTimeout(),
            $coptions->getUsername(),
            $coptions->getPassword()
        );

        return $client;
    }

    // @todo remove zf3
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, SupervisorClient::class);
    }
}
