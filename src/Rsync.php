<?php

namespace edwrodrig\deployer;

/**
 * Class Rsync Deployer
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

    public function __construct() {
        $this->ssh = new ssh\Ssh;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function setTargetDir(string $dir) : Rsync {
        $this->target_dir = $dir;
        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function setSourceDir(string $dir) : Rsync {
        $this->source_dir = $dir;
        return $this;
    }

    /**
     * @param string $executable
     * @return $this
     */
    public function setExecutable(string $executable) : Rsync {
        $this->executable = $executable;
        return $this;
    }

    public function doesExecutableExists() : bool {
        $version_command = sprintf('%s --version', $this->executable);

        if ( $result = Util::runCommand($version_command) ) {
            if ( $result['exit_code'] == 0 )
                return true;
        }

        return false;
    }

    /**
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
        if ( $result = Util::runCommand($command) ) {
            if ( $result['exit_code'] == 0 )
                return $result['std']['out'];
            else
                throw new exception\RsyncException($result['exit_code'], $result['std']['err']);

        } else {
            throw new exception\RsyncException(255, 'proc_open fail');
        }
    }

    /**
     * @return ssh\Ssh
     */
    public function getSsh(): ssh\Ssh
    {
        return $this->ssh;
    }


}

;


