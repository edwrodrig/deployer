<?php
declare(strict_types=1);

namespace edwrodrig\deployer\exception;


use edwrodrig\deployer\ssh\Ssh;
use Exception;

/**
 * Class GitCommandException
 * @api
 * @package edwrodrig\deployer\exception
 */
class GitCommandException extends Exception
{

    const ERRORS = [
        '/nothing to commit, working directory clean/'
    ];

    /**
     * @var string
     */
    private $full_error;

    /**
     * GitCloneException constructor.
     * @internal
     * @param $exit_code
     * @param string $output
     */
    public function __construct($exit_code, string $output = 'Other error')
    {
        $this->full_error = $output;

        foreach ( Ssh::SSH_ERRORS as $error ) {
            if ( preg_match($error, $output, $matches) ) {
                parent::__construct($matches[0], $exit_code);
                return;
            }
        }

        foreach ( self::ERRORS as $error ) {
            if ( preg_match($error, $output, $matches) ) {
                parent::__construct($matches[0], $exit_code);
                return;
            }
        }

        parent::__construct($output, $exit_code);
    }

    /**
     * Return the full error message
     * @api
     * @return string
     */
    public function getFullError() : string {
        return $this->full_error;
    }
}