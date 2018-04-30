<?php

namespace edwrodrig\deployer\exception;

use Exception;

class InvalidHostException extends Exception
{

    /**
     * InvalidHostException constructor.
     * @param string $host
     */
    public function __construct(string $host)
    {
        parent::__construct($host);
    }
}