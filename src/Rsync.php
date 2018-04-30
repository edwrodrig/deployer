<?php

namespace edwrodrig\deployer;

/**
 * Class Rsync Deployer
 * @package edwrodrig\deployer
 */
class Rsync
{

    private $target_host = null;
    private $target_dir = null;
    private $source_dir = null;
    private $executable = 'rsync';

    public function set_target_host(Account $host) : Rsync {
        $this->host = $host;
        return $this;
    }

    public function set_target_dir(string $dir) : Rsync {
        $this->target_dir = $dir;
        return $this;
    }

    public function set_source_dir(string $dir) : Rsync {
        $this->source_dir = $dir;
        return $this;
    }

    public function set_executable(string $executable) {
        $this->executable = $executable;
        return $this;
    }

    /**
     * Get a test command that not makes any changes.
     * It is just thr run command with --dry-run appended (perform a trial run with no changes made)
     * @return string
     */
    public function get_test_command() : string {
        return $this->get_command() . " --dry-run";
    }

    public function get_command() : string {
        return sprintf(
            '%s -rLptgoDvzc -e "ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null" --progress %s %s:%s/live --delete',
            $this->executable,
            $this->source_dir,
            strval($this->target_host),
            $this->target_dir
        );
    }

    public function execute_command() : void {
        $command = $this->get_command();
        passthru($command, $return);
        if ( $return != 0 ) {
            throw new exception\RsyncException($return);
        }
    }

    public function __invoke()
    {
        echo "DEPLOYING using RSYNC\n";
        echo "Uploading files...\n";
        $account = $this->account();
        Utils::call(sprintf('rsync -rLptgoDvzc -e "ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null" --progress %s %s:%s/live --delete', $this->source, $account, $this->target), "Error uploading output [$this->source] to [$account:$this->target]");
        echo "SITE DEPLOYED\n";

    }

}

;


