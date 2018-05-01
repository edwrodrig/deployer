<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 01-05-18
 * Time: 16:10
 */

namespace edwrodrig\deployer\ssh;

/**
 * Class Ssh
 * A ssh connection command builder.
 * You can set the needed files and makes a command for you with the getCommand method.
 * 1.- It's recommended to make your custom configfile with a "target" Host.
 * 2.- Create your private keys with ssh-keygen and register public keys in target Host authorized_keys.
 * 3.- Create a known_hosts file with target host fingerprint
 * @package edwrodrig\deployer\ssh
 */
class Ssh
{

    /**
     * @var string
     */
    private $config_file;

    /**
     * @var string
     */
    private $identity_file;

    /**
     * @var string
     */
    private $known_hosts_file;

    /**
     * @param string $config_file
     * @return Ssh
     */
    public function setConfigFile(string $config_file) : Ssh
    {
        $this->config_file = $config_file;
        return $this;
    }

    /**
     * @param string $identity_file
     * @return Ssh
     */
    public function setIdentityFile(string $identity_file) : Ssh
    {
        $this->identity_file = $identity_file;
        return $this;
    }

    /**
     * @param string $known_hosts_file
     * @return Ssh
     */
    public function setKnownHostsFile(string $known_hosts_file) : Ssh
    {
        $this->known_hosts_file = $known_hosts_file;
        return $this;
    }

    /**
     * @return string
     * @throws exception\InvalidConfigFileException
     * @throws exception\InvalidIdentityFileException
     * @throws exception\InvalidKnownHostsFile
     */
    public function getCommand() : string {
        if ( !file_exists($this->known_hosts_file))
            throw new exception\InvalidKnownHostsFile($this->known_hosts_file);
        if ( !file_exists($this->identity_file))
            throw new exception\InvalidIdentityFileException($this->identity_file);
        if ( !file_exists($this->config_file))
            throw new exception\InvalidConfigFileException($this->config_file);

        return sprintf("ssh -i %s -F %s -o BatchMode=yes -o UserKnownHostsFile=%s",
            $this->identity_file,
            $this->config_file,
            $this->known_hosts_file
            );
    }
}