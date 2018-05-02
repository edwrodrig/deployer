<?php
declare(strict_types=1);

namespace edwrodrig\deployer;

use edwrodrig\deployer\util\Util;

/**
 * Class Rsync Deployer.
 *
 * This class is minded to do a rsync based deploy using ssh. It copies the source files to a remote target directory comparing checksums and deleting not existant files.
 * You need to set the Ssh credentials.
 * Target ssh user, port and host must be set in the ssh config file, in the Ssh object
 * @see Rsync::getSsh() to set ssh credentials
 * @see Rsync::setTargetDir() to set the target dir
 * @see Rsync::setSourceDir() to set the source dir
 * @see Rsync::execute() to execute the task
 * @package edwrodrig\deployer
 */
class Rsync
{

    /**
     * @var string
     */
    private $target_dir = null;

    /**
     * @var string
     */
    private $source_dir = null;

    /**
     * @var string
     */
    private $executable = 'rsync';

    /**
     * @var bool
     */
    private $transform_symlinks_into_targets = false;

    /**
     * @var ssh\Ssh
     */
    private $ssh;

    /**
     * Rsync constructor.
     * Construct a Rsync deployer instance.
     */
    public function __construct() {
        $this->ssh = new ssh\Ssh;
    }

    /**
     * The target dir to copy. It should be relative to the HOME PATH ot the remote account
     * @param string $dir
     * @return $this
     */
    public function setTargetDir(string $dir) : Rsync {
        $this->target_dir = $dir;
        return $this;
    }

    /**
     * The source dir to copy. If you want to copy all files in the file /home/user/some_folder use /home/user/some_folder/*
     * @param string $dir
     * @return $this
     */
    public function setSourceDir(string $dir) : Rsync {
        $this->source_dir = $dir;
        return $this;
    }

    /**
     * Set rsync executable. Needed when it is not in the PATH as rsync
     * @param string $executable
     * @return $this
     */
    public function setExecutable(string $executable) : Rsync {
        $this->executable = $executable;
        return $this;
    }

    /**
     * Check if rsync command exists
     * @return bool
     */
    public function doesExecutableExists() : bool {
        $version_command = sprintf('%s --version', $this->executable);

        if ( $result = Util::runCommand($version_command) ) {
            if ( $result->getExitCode() == 0 )
                return true;
        }

        return false;
    }

    /**
     * When Thins option is enabled, the symlinks are resolved and copied as files in the target remote dir
     * Enables L option in Rsync
     * -L transform symlink into referent file/dir
     * @param bool $enabled
     * @return $this
     */
    public function transformSymlinksIntoTargets(bool $enabled) : Rsync {
        $this->transform_symlinks_into_targets = $enabled;
        return $this;
    }

    /**
     * Get the rsync command to execute.
     * This is used internally and for debug and testing proposes.
     * -r recurse into directories
     * -p preserve permissions
     * -t preserve modification times
     * -v verbose
     * -z compress
     * -c skip based on checksum, not mod-time & size
     * --delete delete extraneous files from dest dirs
     * --progress show progress during transfer
     * @param bool $dry_run --dry-run (perform a trial run with no changes made)
     * @return string
     * @throws ssh\exception\InvalidConfigFileException
     * @throws ssh\exception\InvalidIdentityFileException
     * @throws ssh\exception\InvalidKnownHostsFile
     */
    public function getCommand(bool $dry_run = false) : string {
        return sprintf(
            '%s -r%sptvzc --progress --delete  -e "%s" %s target:%s %s',
            $this->executable,
            $this->transform_symlinks_into_targets ? 'L' : '',
            $this->ssh->getCommand(),
            $this->source_dir,
            $this->target_dir,
            $dry_run ? ' --dry-run' : ''
        );
    }

    /**
     * Execute deploying using Rsync
     * @param bool $test Test execution, execute rsync but not doing any changes, internally it uses --dry-run
     * @return string
     * @throws exception\RsyncException
     * @throws ssh\exception\InvalidConfigFileException
     * @throws ssh\exception\InvalidIdentityFileException
     * @throws ssh\exception\InvalidKnownHostsFile
     */
    public function execute(bool $test = false) : string {
        $command = $this->getCommand($test);
        return self::runRsyncCommand($command);

    }

    /**
     * Run an rsync command. Just wrap the exit codes an standard output and error in Exception
     * @param string $command
     * @return string The standard output, generally the progress of the rsync command
     * @throws exception\RsyncException
     */
    public static  function runRsyncCommand(string $command) : string {
        if ( $result = Util::runCommand($command) ) {
            if ( $result->getExitCode() == 0 )
                return $result->getStdOut();
            else
                throw new exception\RsyncException($result->getExitCode(), $result->getStdErrOrOut());

        } else {
            throw new exception\RsyncException(255, 'proc_open fail');
        }
    }

    /**
     *  Get the ssh object. It manages the ssh connection config, so if you want to change it, for example to set the identify file, you need to retrieve and call its methods.
     * @return ssh\Ssh
     */
    public function getSsh(): ssh\Ssh
    {
        return $this->ssh;
    }


}

;


