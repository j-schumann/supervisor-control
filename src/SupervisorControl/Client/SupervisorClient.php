<?php
/**
 * @copyright   (c) 2014, Vrok
 * @license     http://customlicense CustomLicense
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl\Client;

use SupervisorClient\SupervisorClient as BaseClient;

/**
 * Extends the base client with some management functions.
 */
class SupervisorClient extends BaseClient
{
    // @link http://supervisord.org/subprocess.html#process-states
    const PROCESS_STATE_STARTING  = 10;
    const PROCESS_STATE_RUNNING   = 20;
    const PROCESS_STATE_BACKOFF   = 30;
    const PROCESS_STATE_STOPPING  = 40;
    const PROCESS_STATE_EXITED    = 100;
    const PROCESS_STATE_FATAL     = 200;
    const PROCESS_STATE_UNKNOWN   = 1000;

    /**
     * Caches the result of getAllProcessInfo.
     *
     * @var array
     */
    protected $processInfo = null;

    /**
     * Parses the result of supervisor.getAllConfigInfo to bundle by group.
     *
     * @return array
     */
    public function getGroupConfig()
    {
        $groups = array();
        $config = $this->getAllConfigInfo();

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
     * Checks if the given process exists in the configuration.
     *
     * @param string $name
     * @return bool
     */
    public function processExists($name)
    {
        $config = $this->getAllConfigInfo();
        foreach($config as $process) {
            if ($process['name'] === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves the state of the process given by its name.
     *
     * @todo https://github.com/Supervisor/supervisor/issues/454
     * @param string $name
     * @return int  the process state or PROCESS_STATE_UNKNOWN if process not found
     */
    public function getProcessState($name)
    {
        $infos = $this->getProcessInfos(array($name));
        if (!isset($infos[$name])) {
            return self::PROCESS_STATE_UNKNOWN;
        }

        return (int)$infos[$name]['state'];
    }

    /**
     * Checks if the process given by name is running (or starting).
     *
     * @param string $name  process name to check
     * @return boolean
     */
    public function processIsRunning($name)
    {
        $state = $this->getProcessState($name);
        return ($state == self::PROCESS_STATE_RUNNING || $state == self::PROCESS_STATE_STARTING);
    }

    /**
     * Retrieves the process information for multiple processes at once.
     *
     * Can be used to work around the supervisor API bug with getProcessInfo():
     * @link https://github.com/Supervisor/supervisor/issues/454
     *
     * @param array $names  list of process names to fetch information for
     * @param bool $reload  set to true to force a reload of the information via RPC
     * @return array
     */
    public function getProcessInfos(array $names, $reload = false)
    {
        if ($reload || !$this->processInfo) {
            $this->processInfo = $this->getAllProcessInfo();
        }

        $infos = array();
        foreach ($names as $name) {
            foreach($this->processInfo as $info) {
                if ($info['name'] === $name) {
                    $infos[$name] = $info;
                    break;
                }
            }
        }

        return $infos;
    }
}
