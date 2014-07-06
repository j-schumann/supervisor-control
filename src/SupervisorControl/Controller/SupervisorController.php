<?php
/**
 * @copyright   (c) 2014, Vrok
 * @license     http://customlicense CustomLicense
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl\Controller;

use SupervisorClient\SupervisorClient;
use SupervisorControl\Form\ConfirmationForm;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Module entry point, shows information about the daemon, configured program groups
 * and running processes. Allows to restart the processes by name/group.
 */
class SupervisorController extends AbstractActionController
{
    /**
     * Parses the result of supervisor.getAllConfigInfo to bundle by group.
     *
     * @param \SupervisorClient\SupervisorClient $client
     * @return array
     */
    protected function getGroupConfig(SupervisorClient $client)
    {
        $groups = array();
        $config = $client->getAllConfigInfo();

        foreach($config as $process) {
            if (!isset($groups[$process['group']])) {
                $groups[$process['group']] = array(
                    'name'      => $process['group'],
                    'priority'  => $process['group_prio'],
                    'inuse'     => $process['inuse'],
                    'processes' => array(),
                );
            }

            $groups[$process['group']]['processes'][$process['name']] = array(
                'name'      => $process['name'],
                'priority'  => $process['process_prio'],
                'autostart' => $process['autostart'],
            );
        }

        return $groups;
    }

    /**
     * Shows the overall supervisord status and the configured program groups.
     *
     * @return array
     */
    public function indexAction()
    {
        $client = $this->getServiceLocator()->get('SupervisorClient\SupervisorClient');
        /* @var $client \SupervisorClient\SupervisorClient */

        return array(
            'state'            => $client->getState(),
            'version'          => $client->getSupervisorVersion(),
            'twiddlerSupport'  => $client->isTwiddlerAvailable(),
            'groups'           => $this->getGroupConfig($client),
        );
    }

    /**
     * Restarts the supervisord process after asking for confirmation.
     *
     * @return array|Response
     */
    public function restartAction()
    {
        $form = new ConfirmationForm();
        $form->setData($this->request->getPost());

        if (!$this->request->isPost() || !$form->isValid()) {
            return array(
                'form' => $form,
            );
        }

        $client = $this->getServiceLocator()->get('SupervisorClient\SupervisorClient');
        /* @var $client \SupervisorClient\SupervisorClient */
        $client->restart();
        $this->flashMessenger()->addSuccessMessage('Restart command sent!');
        return $this->redirect()->toRoute('supervisor');
    }

    /**
     * Triggers a start of all configured processes.
     *
     * @return Response
     */
    public function startallAction()
    {
        $client = $this->getServiceLocator()->get('SupervisorClient\SupervisorClient');
        /* @var $client \SupervisorClient\SupervisorClient */
        $client->startAllProcesses();
        $this->flashMessenger()->addSuccessMessage('All processes started!');
        return $this->redirect()->toRoute('supervisor');
    }

    /**
     * Stops all processes after asking for confirmation.
     *
     * @return array|Response
     */
    public function stopallAction()
    {
        $form = new ConfirmationForm();
        $form->setData($this->request->getPost());

        if (!$this->request->isPost() || !$form->isValid()) {
            return array(
                'form' => $form,
            );
        }

        $client = $this->getServiceLocator()->get('SupervisorClient\SupervisorClient');
        /* @var $client \SupervisorClient\SupervisorClient */
        $client->stopAllProcesses();
        $this->flashMessenger()->addSuccessMessage('All processes stopped!');
        return $this->redirect()->toRoute('supervisor');
    }

    /**
     * Lists information about the given group and its processes.
     *
     * @return array|Response
     */
    public function groupAction()
    {
        $name = $this->params('name');
        $client = $this->getServiceLocator()->get('SupervisorClient\SupervisorClient');
        /* @var $client \SupervisorClient\SupervisorClient */

        $groups = $this->getGroupConfig($client);
        if (!isset($groups[$name])) {
            $this->flashMessenger()->addErrorMessage('Group "'.$name.'" not found!');
            return $this->redirect()->toRoute('supervisor');
        }

        // find the processInfo for each of the groups processes

        // @todo: we do not use $client->getProcessInfo() as it fails with BAD_NAME
        // when the group uses the "numprocs" setting and the processes are named
        // "programmgroup-00", "programmgroup-01", ...
        // @link https://github.com/Supervisor/supervisor/issues/454
        // Also this would require multiple API calls, getAllProcessInfo is only one.
        $group = $groups[$name];
        $processInfo = $client->getAllProcessInfo();
        foreach($group['processes'] as $process) {
            foreach($processInfo as $info) {
                if ($info['name'] === $process['name']) {
                    $group['processes'][$process['name']]['info'] = $info;
                    break;
                }
            }
        }

        return array(
            'name'  => $name,
            'group' => $group,
        );
    }

    /**
     * Triggers a start of all processes of the given group.
     *
     * @return Response
     */
    public function startgroupAction()
    {
        $name = $this->params('name');

        $client = $this->getServiceLocator()->get('SupervisorClient\SupervisorClient');
        /* @var $client \SupervisorClient\SupervisorClient */

        try {
            $client->startProcessGroup($name);
        }
        catch(\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Command failed with message: "'
                    .$e->getMessage().'"');
            return $this->redirect()->toRoute('supervisor');
        }

        $this->flashMessenger()->addSuccessMessage('All processes of group "'.$name.'" started!');
        return $this->redirect()->toRoute('supervisor/group', array('name' => $name));
    }

    /**
     * Stops all processes of the given group after asking for confirmation.
     *
     * @return array|Response
     */
    public function stopgroupAction()
    {
        $name = $this->params('name');
        $form = new ConfirmationForm();
        $form->setData($this->request->getPost());

        if (!$this->request->isPost() || !$form->isValid()) {
            return array(
                'name' => $name,
                'form' => $form,
            );
        }

        $client = $this->getServiceLocator()->get('SupervisorClient\SupervisorClient');
        /* @var $client \SupervisorClient\SupervisorClient */

        try {
            $client->stopProcessGroup($name);
        }
        catch(\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Command failed with message: "'
                    .$e->getMessage().'"');
            return $this->redirect()->toRoute('supervisor');
        }

        $this->flashMessenger()->addSuccessMessage('All processes of group "'.$name.'" stopped!');
        return $this->redirect()->toRoute('supervisor/group', array('name' => $name));
    }

    /**
     * Triggers a start of the process given by name.
     *
     * @return Response
     */
    public function startProcessAction()
    {
        $name = $this->params('name');

        $client = $this->getServiceLocator()->get('SupervisorClient\SupervisorClient');
        /* @var $client \SupervisorClient\SupervisorClient */

        try {
            $client->startProcess($name);
        }
        catch(\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Command failed with message: "'
                    .$e->getMessage().'"');
            return $this->redirect()->toRoute('supervisor');
        }

        $this->flashMessenger()->addSuccessMessage('Process "'.$name.'" started!');
        return $this->redirect()->toRoute('supervisor');
    }

    /**
     * Triggers a stop of the process given by name after asking for confirmation.
     *
     * @return array|Response
     */
    public function stopProcessAction()
    {
        $name = $this->params('name');
        $form = new ConfirmationForm();
        $form->setData($this->request->getPost());

        if (!$this->request->isPost() || !$form->isValid()) {
            return array(
                'name' => $name,
                'form' => $form,
            );
        }

        $client = $this->getServiceLocator()->get('SupervisorClient\SupervisorClient');
        /* @var $client \SupervisorClient\SupervisorClient */

        try {
            $client->stopProcess($name);
        }
        catch(\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Command failed with message: "'
                    .$e->getMessage().'"');
            return $this->redirect()->toRoute('supervisor');
        }

        $this->flashMessenger()->addSuccessMessage('Processes "'.$name.'" stopped!');
        return $this->redirect()->toRoute('supervisor');
    }
}
