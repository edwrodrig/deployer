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
        $this->setUser($user);
        $this->setHost($host);
    }

    /**
     * @param string $user
     * @return Account
     */
    public function setUser(string $user) : Account {
        $user = trim($user);
        $this->user = empty($user) ? null : $user;
        return $this;
    }

    /**
     * @param string $host
     * @return Account
     * @throws exception\InvalidHostException
     */
    public function setHost(string $host) : Account {
        $host = trim($host);

        if ( empty($host) )
            throw new exception\InvalidHostException($host);


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