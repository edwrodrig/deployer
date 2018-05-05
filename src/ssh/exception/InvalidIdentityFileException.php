<?php
declare(strict_types=1);

namespace edwrodrig\deployer\ssh\exception;


use Exception;

/**
 * Class InvalidIdentityFileException
 * @api
 * @package edwrodrig\deployer\ssh\exception
 */
class InvalidIdentityFileException extends Exception {

    /**
     * InvalidIdentityFileException constructor.
     * @internal
     * @param string $identity_file_name
     */
    public function __construct(string $identity_file_name) {
        parent::__construct($identity_file_name);
    }
}