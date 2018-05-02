<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 01-05-18
 * Time: 22:23
 */

namespace edwrodrig\deployer\exception;


use Exception;

class GitCommandException extends Exception
{

    /**
     * GitCloneException constructor.
     * @param $exit_code
     * @param $err
     */
    public function __construct($exit_code, string $output = 'Other error')
    {
        parent::__construct($output, $exit_code);
    }
}