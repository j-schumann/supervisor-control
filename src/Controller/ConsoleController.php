<?php

/**
 * @copyright   (c) 2014-16, Vrok
 * @license     http://customlicense CustomLicense
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl\Controller;

use SupervisorControl\Client\SupervisorClient;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Handles the console routes, used for cron jobs.
 */
class ConsoleController extends AbstractActionController
{
    const EVENT_PROCESSRUNNING    = 'processRunning';
    const EVENT_PROCESSNOTRUNNING = 'processNotRunning';

    /**
     * @var SupervisorClient
     */
    protected $supervisorClient = null;

    /**
     * Class constructor - stores the given dependency
     *
     * @param SupervisorClient $sc
     */
    public function __construct(SupervisorClient $sc)
    {
        $this->supervisorClient = $sc;
    }

    /**
     * Checks if the process specified by name is currently running.
     *
     * @triggers processRunning
     * @triggers processNotRunning
     */
    public function checkProcessAction()
    {
        $name      = $this->params('name');
        $isRunning = $this->supervisorClient->isProcessRunning($name);
        $info      = $this->supervisorClient->getProcessInfo($name);

        if ($isRunning) {
            $this->getEventManager()->trigger(
                self::EVENT_PROCESSRUNNING,
                $this->supervisorClient,
                [
                    'processName' => $name,
                    'info'        => $info,
                ]
            );

            echo date('Y-m-d H:i:s').": process '$name' is running!\n";
        } else {
            $this->getEventManager()->trigger(
                self::EVENT_PROCESSNOTRUNNING,
                $this->supervisorClient,
                [
                    'processName' => $name,
                    'info'        => $info,
                ]
            );

            echo date('Y-m-d H:i:s').": process '$name' is NOT running!\n";
        }
    }
}
