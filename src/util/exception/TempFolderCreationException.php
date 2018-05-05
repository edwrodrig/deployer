<?php
declare(strict_types=1);

namespace edwrodrig\deployer\util\exception;


use Exception;

/**
 * Class TempFolderCreationException
 * @package edwrodrig\deployer\util\exception
 * @api
 */
class TempFolderCreationException extends Exception
{
    /**
     * TempFolderCreationException constructor.
     * @param string $temp_folder_name
     * @internal
     */
    public function __construct(string $temp_folder_name) {
        parent::__construct($temp_folder_name);
    }
}