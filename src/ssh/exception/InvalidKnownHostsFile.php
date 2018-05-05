<?php
declare(strict_types=1);

namespace edwrodrig\deployer\ssh\exception;


use Exception;

/**
 * Class InvalidKnownHostsFile
 * @api
 * @package edwrodrig\deployer\ssh\exception
 */
class InvalidKnownHostsFile extends Exception
{
    /**
     * InvalidKnownHostsFile constructor.
     * @internal
     * @param string $known_hosts_file_name_name
     */
    public function __construct(string $known_hosts_file_name_name) {
        parent::__construct($known_hosts_file_name_name);
    }
}