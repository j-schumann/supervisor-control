<?php
/**
 * @copyright   (c) 2014, Vrok
 * @license     http://customlicense CustomLicense
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Handles the console routes, used for cron jobs.
 */
class ConsoleController extends AbstractActionController
{
    const EVENT_PROCESSRUNNING    = 'processRunning';
    const EVENT_PROCESSNOTRUNNING = 'processNotRunning';

    /**
     * Checks if the process specified by name is currently running.
     *
     * @triggers processRunning
     * @triggers processNotRunning
     */
    public function checkProcessAction()
    {
        $client = $this->getServiceLocator()->get('SupervisorClient');
        /* @var $client \SupervisorControl\Client\SupervisorClient */

        $name = $this->params('name');
        $isRunning = $client->isProcessRunning($name);
        $info = $client->getProcessInfo($name);

        if ($isRunning) {
            $this->getEventManager()->trigger(
                self::EVENT_PROCESSRUNNING,
                $client,
                array(
                    'processName' => $name,
                    'info'        => $info,
                )
            );

            // no output if everything is OK
            //echo "process '$name' is running!\n";
        }
        else {
            $this->getEventManager()->trigger(
                self::EVENT_PROCESSNOTRUNNING,
                $client,
                array(
                    'processName' => $name,
                    'info'        => $info,
                )
            );

            echo "process '$name' is NOT running!\n";
        }
    }
}
