<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 29-04-18
 * Time: 12:41
 */

namespace edwrodrig\deployer;


class Account
{

    /**
     * @var string The user
     */
    private $user;

    /**
     * @var string the host
     */
    private $host;

    /**
     * Account constructor.
     * @param string $user
     * @param string $host
     * @throws exception\InvalidHostException
     */
    public function __construct(string $user, string $host) {
        $this->set_user($user);
        $this->set_host($host);
    }

    /**
     * @param string $user
     * @return Account
     */
    public function set_user(string $user) : Account {
        $user = trim($user);
        $this->user = empty($user) ? null : $user;
        return $this;
    }

    /**
     * @param string $host
     * @return Account
     * @throws exception\InvalidHostException
     */
    public function set_host(string $host) : Account {
        $host = trim($host);

        if ( empty($host) )
            throw new exception\InvalidHostException($host);
        else
            $this->host = $host;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() : string {

        return sprintf('%s@%s',
            $this->user,
            $this->host
        );
    }

}