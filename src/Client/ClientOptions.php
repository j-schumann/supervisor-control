<?php

/**
 * @copyright   (c) 2014-16, Vrok
 * @license     http://customlicense CustomLicense
 * @author      Jakob Schumann <schumann@vrok.de>
 */

namespace SupervisorControl\Client;

use Zend\Stdlib\AbstractOptions;

/**
 * Options class for the mondalaci/supervisor-client.
 */
class ClientOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $hostname = '127.0.0.1';

    /**
     * @var int
     */
    protected $port = 9001;

    /**
     * @var int
     */
    protected $timeout = null;

    /**
     * @var string
     */
    protected $username = null;

    /**
     * @var string
     */
    protected $password = null;

    /**
     * Set the hostname to connect to.
     *
     * @param string $hostname
     *
     * @return self
     */
    public function setHostname($hostname)
    {
        $this->hostname = (string) $hostname;

        return $this;
    }

    /**
     * Get the hostname to connect to.
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set the port to connect to.
     *
     * @param int $port
     *
     * @return self
     */
    public function setPort($port)
    {
        $this->port = (int) $port;

        return $this;
    }

    /**
     * Get the port to connect to.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the connection timeout.
     *
     * @param int $timeout
     *
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int) $timeout;

        return $this;
    }

    /**
     * Get the connection timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set the username to use.
     *
     * @param string $username
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = (string) $username;

        return $this;
    }

    /**
     * Get the username to use.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the password to use.
     *
     * @param string $password
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = (string) $password;

        return $this;
    }

    /**
     * Get the password to use.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
