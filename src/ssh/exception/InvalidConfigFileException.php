<?php
declare(strict_types=1);

namespace edwrodrig\deployer\ssh\exception;


use Exception;

/**
 * Class InvalidConfigFileException
 * @api
 * @package edwrodrig\deployer\ssh\exception
 */
class InvalidConfigFileException extends Exception
{
    /**
     * InvalidConfigFileException constructor.
     * @param string $identity_file_name
     * @internal
     */
    public function __construct(string $identity_file_name) {
        parent::__construct($identity_file_name);
    }
}