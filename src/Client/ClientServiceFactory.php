<?php

/**
 * @copyright   (c) 2014-16, Vrok
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl\Client;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Creates an instance of the supervisor client.
 */
class ClientServiceFactory implements FactoryInterface
{
    /**
     * Creates a client instance.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options
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
}
