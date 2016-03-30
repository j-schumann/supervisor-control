<?php

/**
 * @copyright   (c) 2014-16, Vrok
 * @license     http://customlicense CustomLicense
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl\Controller;

use SupervisorControl\Client\SupervisorClient;
use SupervisorControl\Form\ConfirmationForm;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Module entry point, shows information about the daemon, configured program groups
 * and running processes. Allows to restart the processes by name/group.
 */
class SupervisorController extends AbstractActionController
{
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
     * Shows the overall supervisord status and the configured program groups.
     *
     * @return array
     */
    public function indexAction()
    {
        return [
            'state'           => $this->supervisorClient->getState(),
            'version'         => $this->supervisorClient->getSupervisorVersion(),
            'twiddlerSupport' => $this->supervisorClient->isTwiddlerAvailable(),
            'groups'          => $this->supervisorClient->getGroupConfig(),
        ];
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
            return [
                'form' => $form,
            ];
        }

        $this->supervisorClient->restart();
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
        $this->supervisorClient->startAllProcesses();
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
            return [
                'form' => $form,
            ];
        }

        $this->supervisorClient->stopAllProcesses();
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
        $name   = $this->params('name');

        $groups = $this->supervisorClient->getGroupConfig();
        if (!isset($groups[$name])) {
            $this->flashMessenger()->addErrorMessage('Group "'.$name.'" not found!');

            return $this->redirect()->toRoute('supervisor');
        }

        $group          = $groups[$name];
        $group['infos'] = $this->supervisorClient->getProcessInfos(array_keys($group['processes']));

        return [
            'name'  => $name,
            'group' => $group,
        ];
    }

    /**
     * Triggers a start of all processes of the given group.
     *
     * @return Response
     */
    public function startgroupAction()
    {
        $name = $this->params('name');

        try {
            $this->supervisorClient->startProcessGroup($name);
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Command failed with message: "'
                    .$e->getMessage().'"');

            return $this->redirect()->toRoute('supervisor');
        }

        $this->flashMessenger()->addSuccessMessage('All processes of group "'.$name.'" started!');

        return $this->redirect()->toRoute('supervisor/group', ['name' => $name]);
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
            return [
                'name' => $name,
                'form' => $form,
            ];
        }

        try {
            $this->supervisorClient->stopProcessGroup($name);
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Command failed with message: "'
                    .$e->getMessage().'"');

            return $this->redirect()->toRoute('supervisor');
        }

        $this->flashMessenger()->addSuccessMessage('All processes of group "'.$name.'" stopped!');

        return $this->redirect()->toRoute('supervisor/group', ['name' => $name]);
    }

    /**
     * Triggers a start of the process given by name.
     *
     * @return Response
     */
    public function startProcessAction()
    {
        $name = $this->params('name');

        try {
            $this->supervisorClient->startProcess($name);
        } catch (\Exception $e) {
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
            return [
                'name' => $name,
                'form' => $form,
            ];
        }

        try {
            $this->supervisorClient->stopProcess($name);
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Command failed with message: "'
                    .$e->getMessage().'"');

            return $this->redirect()->toRoute('supervisor');
        }

        $this->flashMessenger()->addSuccessMessage('Processes "'.$name.'" stopped!');

        return $this->redirect()->toRoute('supervisor');
    }
}
