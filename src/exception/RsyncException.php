<?php
declare(strict_types=1);

namespace edwrodrig\deployer\exception;

use edwrodrig\deployer\ssh\Ssh;
use Exception;

/**
 * Class RsyncException
 * @api
 * @package edwrodrig\deployer\exception
 */
class RsyncException extends Exception
{

    /**
     * Exit codes of rsync.
     * @see https://lxadm.com/Rsync_exit_codes
     */
    const EXIT_CODES = [
        1 => 'Syntax or usage error',
        2 => 'Protocol incompatibility',
        3 => 'Errors selecting input/output files, dirs',
        4 => 'Requested  action not supported: an attempt was made to manipulate 64-bit files on a platform that cannot support them; or an option was specified that is supported by the client and not by the server.',
        5 => 'Error starting client-server protocol',
        6 => 'Daemon unable to append to log-file',
        10 => 'Error in socket I/O',
        11 => 'Error in file I/O',
        12 => 'Error in rsync protocol data stream',
        13 => 'Errors with program diagnostics',
        14 => 'Error in IPC code',
        20 => 'Received SIGUSR1 or SIGINT',
        21 => 'Some error returned by waitpid()',
        22 => 'Error allocating core memory buffers',
        23 => 'Partial transfer due to error',
        24 => 'Partial transfer due to vanished source files',
        25 => 'The --max-delete limit stopped deletions',
        30 => 'Timeout in data send/receive',
        35 => 'Timeout waiting for daemon connection',
        127 => 'Executable not found'
    ];

    /**
     * @var string
     */
    private $full_error;

    /**
     * RsyncException constructor.
     * @internal
     * @param int $exit_code Rsync exit code
     * @param string $output
     */
    public function __construct(int $exit_code, string $output = 'Other error')
    {
        $this->full_error = $output;

        foreach ( Ssh::SSH_ERRORS as $error ) {
            if ( preg_match($error, $output, $matches) ) {
                parent::__construct($matches[0], $exit_code);
                return;
            }
        }

        parent::__construct(self::EXIT_CODES[$exit_code] ?? $output, $exit_code);
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