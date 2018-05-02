<?php
namespace edwrodrig\deployer;

class Github {

    /**
     * @var string
     */
    private $target_user;

    /**
     * @var string
     */
    private $target_repo_name;

    /**
     * @var string
     */
    private $target_repo_branch = 'master';

    /**
     * @var string
     */
    private $source_dir;

    /**
     * @var string
     */
    private $executable = 'git';

    /**
     * @var string
     */
    private $commit_message = 'Automatic deploy';

    /**
     * @var string
     */
    private $email = 'noreply@deployer.none';


    /**
     * @var ssh\Ssh
     */
    private $ssh;

    public function __construct() {
        $this->ssh = new ssh\Ssh;
        $this->ssh->setConfigFile(__DIR__ . '/../files/github_credentials/config');
        $this->ssh->setKnownHostsFile(__DIR__ . '/../files/github_credentials/known_hosts');
    }

    public function getSsh() : ssh\Ssh {
        return $this->ssh;
    }

    public function setTargetUser(string $user) : Github {
        $this->target_user = $user;
        return $this;
    }

    public function getCloneCommand(string $folder_name) {
        return sprintf('%s clone github:%s/%s %s -b %s',
            $this->executable,
            $this->target_user,
            $this->target_repo_name,
            $folder_name,
            $this->target_repo_branch
        );
    }

    /**
     * @param bool $test
     * @return string
     * @throws exception\RsyncException
     * @throws exception\TempFolderCreationException
     * @throws ssh\exception\InvalidConfigFileException
     * @throws ssh\exception\InvalidIdentityFileException
     * @throws ssh\exception\InvalidKnownHostsFile
     * @throws exception\GitCommandException
     */
    public function execute(bool $test = false) : string {
        $folder_name = Util::createTempFolder();

        $std_out = '';

        $env = [
            'GIT_SSH_COMMAND' => $this->ssh->getCommand(),
            'EMAIL' => $this->email
        ];

        $command = $this->getCloneCommand($folder_name);
        $std_out.= self::runGitCommand($command, $folder_name, $env);

        $command = $this->getCopyCommand($folder_name);
        $std_out.= Rsync::runRsyncCommand($command);

        $std_out.= self::runGitCommand('git add -A', $folder_name, $env);

        $std_out.= self::runGitCommand(
            sprintf('git commit -m "%s"', $this->commit_message),
            $folder_name, $env
        );

        $std_out.= self::runGitCommand(
            sprintf('git push origin %s%s', $this->target_repo_branch, $test ? ' --dry-run' : ''),
            $folder_name, $env
        );

        return $std_out;
    }

    /**
     * @param string $command
     * @param string $current_working_dir
     * @param array $env
     * @return mixed
     * @throws exception\GitCommandException
     */
    public static function runGitCommand(string $command, string $current_working_dir, array $env) {
        if ( $result = Util::runCommand($command, $current_working_dir, $env) ) {
            if ( $result['exit_code'] == 0 ) {
                return $result['std']['out'];
            } else
                $std_err = empty($result['std']['err']) ? $result['std']['out'] : $result['std']['err'];
                throw new exception\GitCommandException($result['exit_code'], $std_err);

        } else {
            throw new exception\GitCommandException(255, 'proc_open fail');
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
     * @param string $target
     * @return string
     */
    public function getCopyCommand(string $target) {
        return sprintf('rsync -rptvz %s %s --exclude=.git* --progress --delete',
            $this->source_dir . '/.',
            $target
        );
    }

    /**
     * @param string $target_repo_name
     * @return $this
     */
    public function setTargetRepoName(string $target_repo_name): Github
    {
        $this->target_repo_name = $target_repo_name;
        return $this;
    }

    /**
     * @param string $target_repo_branch
     * @return $this
     */
    public function setTargetRepoBranch(string $target_repo_branch): Github
    {
        $this->target_repo_branch = $target_repo_branch;
        return $this;
    }

    /**
     * @param string $source_dir
     * @return $this
     */
    public function setSourceDir(string $source_dir): Github
    {
        $this->source_dir = $source_dir;
        return $this;
    }

}


