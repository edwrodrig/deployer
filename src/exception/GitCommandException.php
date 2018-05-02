<?php
declare(strict_types=1);

namespace edwrodrig\deployer\exception;


use Exception;

class GitCommandException extends Exception
{

    const ERRORS = [
        '/ssh: Could not resolve hostname \S*: Name or service not known/',
        '/Can\'t open user config file \S*/',
        '/Warning: Identity file \S* not accessible: No such file or directory./',
        '/Host key verification failed./',
        '/Permission denied \(\S*/',
        '/nothing to commit, working directory clean/'
    ];

    /**
     * GitCloneException constructor.
     * @param $exit_code
     * @param string $output
     */
    public function __construct($exit_code, string $output = 'Other error')
    {
        foreach ( self::ERRORS as $error ) {
            if ( preg_match($error, $output, $matches) ) {
                parent::__construct($matches[0], $exit_code);
                return;
            }
        }

        parent::__construct($output, $exit_code);
    }
}