<?php
namespace edwrodrig\deployer;

class Github {

    private $target_user = null;
    private $target_repo_name = null;
    private $target_repo_branch = 'master';
    private $source_dir = 'output';
    private $executable = 'git';
    private $commit_message = 'Automatic deploy';

    /**
     * @var ssh\Ssh
     */
    private $ssh;

    public function __construct() {
        $this->ssh = new ssh\Ssh;
    }

    public function getSsh() : ssh\Ssh {
        return $this->ssh;
    }

    public function getCloneCommand(string $folder_name) {
        return sprintf('git clone git@github.com:%s/%s.git %s -b %s %s',
            $this->target_user,
            $this->target_repo_name,
            $this->folder_name,
            $this->target_repo_branch
        );
    }

    public function execute(bool $test = false) : string {
        $folder_name = Util::createTempFolder();

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
     * -r recurse into directories
     * -p preserve permissions
     * -t preserve modification times
     * -v verbose
     * -z compress
     * -c skip based on checksum, not mod-time & size
     * --delete delete extraneous files from dest dirs
     * --progress show progress during transfer
     * --exclude=.git*
     */
    public function getCopyCommand(string $target) {
        return sprintf('rsync -rptvz %s target --exclude=.git* --progress --delete',
            $this->source_dir . '/.',
            $target
        );
    }

    public function getUpdateCommand(bool $dry_run = false) {
        return sprintf('git add -A; git commit -m "%s"; git push origin %s%s',
            $this->commit_message,
            $this->target_repo_brach,
            $dry_run ? ' --dry-run' : ''
        );
    }

public function __invoke() {
  echo "DEPLOYING to GITHUB\n";
  echo "Uploading files...\n";
  Utils::call('rm -rf /tmp/gitrepo', 'Error removing temporary folder');
  Utils::call(sprintf('git clone git@github.com:%s/%s.git /tmp/gitrepo', $this->user, $this->target), "Error cloning git repository $this->user@$this->target");
  Utils::call(sprintf('cd /tmp/gitrepo; git checkout %s', $this->branch), 'Fail to change branch');
  Utils::call("rm -rf /tmp/gitrepo/*; cp -rf $this->source/* /tmp/gitrepo/", 'Error preparing commit');
  Utils::call(sprintf('cd /tmp/gitrepo; git add -A; git commit -a -m "Automatic deploy"; git push origin %s', $this->branch), 'Error uploading to github');
  Utils::call('rm -rf /tmp/gitrepo', 'Error removing temporary file'); 
  echo "SITE DEPLOYED\n";

}

};


