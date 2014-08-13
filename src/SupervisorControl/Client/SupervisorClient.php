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
     * Caches the result of getAllProcessInfo fetched by {@link getProcessInfos}
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
            $groupName = $process['group'];

            if (!isset($groups[$groupName])) {
                $groups[$groupName] = array(
                    'name'      => $groupName,
                    'priority'  => $process['group_prio'],
                    'inuse'     => $process['inuse'],
                    'processes' => array(),
                );
            }

            $groups[$groupName]['processes'][$process['name']] = array(
                'name'      => $process['name'],
                'priority'  => $process['process_prio'],
                'autostart' => $process['autostart'],
            );
        }

        return $groups;
    }

    /**
     * Retrieves the config information for the given process (may be the short name
     * or the FQN with the group name prefixed: "group:process")
     *
     * @param string $name
     * @return array
     */
    public function getProcessConfig($name)
    {
        // the config only contains the name without the group prefix
        $parts = explode(':', $name);
        if (count($parts) == 2) {
            $name = $parts[1];
        }

        $config = $this->getAllConfigInfo();
        foreach($config as $process) {
            if ($process['name'] === $name) {
                return $process;
            }
        }

        return null;
    }

    /**
     * Checks if the given process exists in the configuration.
     *
     * @param string $name
     * @return bool
     */
    public function processExists($name)
    {
        return (bool)$this->getProcessConfig($name);
    }

    /**
     * Retrieve the FQN for the given process.
     * The FQN consists of "group:process" and must be used for program groups with
     * numprocs > 0 or getProcessInfo() will fail.
     *
     * @param string $name
     * @return string   the FQN or null if the process wasn't found in the config
     */
    public function getProcessFQN($name)
    {
        $parts = explode(':', $name);
        if (count($parts) == 2) {
            return $name;
        }

        $config = $this->getProcessConfig($name);
        if (!$config) {
            return null;
        }

        return $config['group'].':'.$config['name'];
    }

    /**
     * {@inheritdoc}
     * Replaces a short name with the FQN ("group:process") before querying the API.
     *
     * @param string $processName
     * @return array or null if the process was not found
     */
    public function getProcessInfo($processName)
    {
        $fqn = $this->getProcessFQN($processName);
        if (!$fqn) {
            return null;
        }

        return parent::getProcessInfo($fqn);
    }

    /**
     * Retrieves the state of the process given by its name.
     *
     * @param string $name
     * @return int  the process state or PROCESS_STATE_UNKNOWN if process not found
     */
    public function getProcessState($name)
    {
        $fqn = $this->getProcessFQN($name);
        if (!$fqn) {
            return self::PROCESS_STATE_UNKNOWN;
        }

        try {
            $infos = $this->getProcessInfo($fqn);
        }
        catch(\Exception $e) {
            // most probable reason is "BAD NAME"
            return self::PROCESS_STATE_UNKNOWN;
        }

        return (int)$infos['state'];
    }

    /**
     * Checks if the process given by name is running (or starting).
     *
     * @param string $name  process name to check
     * @return boolean
     */
    public function isProcessRunning($name)
    {
        $state = $this->getProcessState($name);
        // @see https://github.com/Supervisor/supervisor/blob/master/supervisor/states.py
        // RUNNING_STATES (BACKOFF is not optimal but listed there...)
        return ($state == self::PROCESS_STATE_RUNNING
                || $state == self::PROCESS_STATE_STARTING
                || $state == self::PROCESS_STATE_BACKOFF);
    }

    /**
     * Retrieves the process information for multiple processes at once.
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
            // the result only contains the name without the group prefix
            $parts = explode(':', $name);
            if (count($parts) == 2) {
                $name = $parts[1];
            }

            foreach($this->processInfo as $info) {
                if ($info['name'] === $name) {
                    $infos[$name] = $info;
                    break;
                }
            }
        }

        return $infos;
    }
    
    /**
     * Do a request to the supervisor XML-RPC API
     *
     * @param string $namespace The namespace of the request
     * @param string $method The method in the namespace
     * @param mixed $args Optional arguments
     */
    protected function _doRequest($namespace, $method, $args)
    {
        // Create the authorization header.
        $authorization = '';
        if (!is_null($this->_username) && !is_null($this->_password)) {
            $authorization = "\r\nAuthorization: Basic " . base64_encode($this->_username . ':' . $this->_password);
        }

        $host = '';
        // not an unix socket
        if (strpos($this->_hostname, '/') === false) {
            $host = $this->_hostname;

            if ($this->_port != -1) {
                $host .= ':' . $this->_port;
            }
        }

        // Create the HTTP request.
        $xml_rpc = \xmlrpc_encode_request("$namespace.$method", $args, array('encoding' => 'utf-8'));
        $httpRequest = "POST /RPC2 HTTP/1.1\r\n" .
            "Host: $host\r\n" .
            "Content-Length: " . strlen($xml_rpc) .
            $authorization .
            "\r\n\r\n" .
            $xml_rpc;

        // Write the request to the socket.
        fwrite($this->_getSocket(), $httpRequest);
    }
}
