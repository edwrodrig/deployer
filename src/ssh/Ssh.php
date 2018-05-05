<?php
declare(strict_types=1);

namespace edwrodrig\deployer\ssh;

/**
 * Class Ssh. A ssh connection command builder.
 *
 * You can set the needed files and makes a command for you with the getCommand method.
 *
 * * It's recommended to make your custom configfile with a "target" Host.
 * * Create your private keys with ssh-keygen and register public keys in target Host authorized_keys.
 * * Create a known_hosts file with target host fingerprint
 * @api
 * @see Ssh::setConfigFile() to set the config file
 * @see Ssh::setKnownHostsFile() to set the known hosts file
 * @see Ssh::setIdentityFile() to set the identity file
 * @package edwrodrig\deployer\ssh
 */
class Ssh
{

    /**
     * @var string
     */
    private $config_file;

    /**
     * @var ?string
     */
    private $identity_file = null;

    /**
     * @var string
     */
    private $known_hosts_file;

    /**
     * Some error messages from a SSH command
     */
    const SSH_ERRORS = [
        '/Could not resolve hostname \S*: Name or service not known/',
        '/Can\'t open user config file \S*/',
        '/Warning: Identity file \S* not accessible: No such file or directory./',
        '/Host key verification failed./',
        '/Permission denied \(\S*/'
    ];

    /**
     * Ssh constructor.
     * @internal User should never create a instance of this object, always should be created for other api objects
     */
    public function __construct() {}

    /**
     * Set the ssh config file.
     *
     * @see https://github.com/edwrodrig/deployer/blob/master/tests/files/correct/config Example of file
     * @param string $config_file
     * @api
     * @return Ssh
     */
    public function setConfigFile(string $config_file) : Ssh
    {
        $this->config_file = $config_file;
        return $this;
    }

    /**
     * Set the ssh identity file.
     *
     * Set to null if you want to use default identity.
     * Generate new identity files with ssh-keygen. Append the id_rsa.pub in the ~/.ssh/authorized_keys file in the target ssh account
     * @see https://github.com/edwrodrig/deployer/blob/master/tests/files/correct/id_rsa Example of a identity file
     * @api
     * @param string|null $identity_file
     * @return Ssh
     */
    public function setIdentityFile(?string $identity_file) : Ssh
    {
        $this->identity_file = $identity_file;
        return $this;
    }

    /**
     * Set the known hosts file.
     *
     * This file is used to check the target host and prevent man in the middle vulnerabilities.
     * @see https://github.com/edwrodrig/deployer/blob/master/tests/files/correct/known_hosts Example of known hosts file
     * @api
     * @param string $known_hosts_file
     * @return Ssh
     */
    public function setKnownHostsFile(string $known_hosts_file) : Ssh
    {
        $this->known_hosts_file = $known_hosts_file;
        return $this;
    }

    /**
     * Return a ssh command using the different configuration files. All files must exist or this would fail
     * @internal This could be used for debug
     * @source 4 6 If identity is present then create a command line option to set it
     * @return string the command itself
     * @throws exception\InvalidConfigFileException
     * @throws exception\InvalidIdentityFileException
     * @throws exception\InvalidKnownHostsFile
     */
    public function getCommand() : string {
        $identity_section =  '';

        if ( !is_null($this->identity_file) ) {
            /** @var $identity_file string */
            $identity_file = $this->identity_file;

            if (file_exists($identity_file)) {
                $identity_section = sprintf('-i %s ', $identity_file);
            } else {
                /** @noinspection PhpInternalEntityUsedInspection */
                throw new exception\InvalidIdentityFileException($identity_file);
            }
        }

        if ( !file_exists($this->known_hosts_file)) {
            /** @noinspection PhpInternalEntityUsedInspection */
            throw new exception\InvalidKnownHostsFile($this->known_hosts_file);
        }

        if ( !file_exists($this->config_file)) {
            /** @noinspection PhpInternalEntityUsedInspection */
            throw new exception\InvalidConfigFileException($this->config_file);
        }

        return sprintf("ssh %s-F %s -o BatchMode=yes -o UserKnownHostsFile=%s",
            $identity_section,
            $this->config_file,
            $this->known_hosts_file
            );
    }
}