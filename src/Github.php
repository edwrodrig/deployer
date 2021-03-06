<?php
declare(strict_types=1);

namespace edwrodrig\deployer;

use /** @noinspection PhpInternalEntityUsedInspection */
    edwrodrig\deployer\util\Util;

/**
 * Class Github
 * Github deployer class. This class is minded to deploy github pages on github.
 *
 * This class just clone a github repository, then copy specified files to the cloned repository folder using rsync, and then commiting and pushing the changes to the origin.
 * Rsync check the differences based on checksums and deletes files that are not in the source files.
 * You need to set the github {@see Github::setTargetUser() user},
 * the github {@see Github::setTargetRepoName() repository name} and {@see GithubTargetRepoBranch() branch}.
 * Also need to set the {@see Github::setSourceDir source directory} to commit.
 * When all is set you can {@see Github::execute() execute} the deploy.
 * The ssh github credentials and known_host are setted by default.
 * But you need to {@see Github::getSsh() set the identity file} for authentication
 * @api
 * @package edwrodrig\deployer
 * @see https://github.com/edwrodrig/deployer/blob/master/examples/github_deploy.php Github deploy example
 */
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

    /**
     * Github constructor
     * Construct a Github deployer.
     * @api
     */
    public function __construct() {
        $this->ssh = new ssh\Ssh;
        $this->ssh->setConfigFile(__DIR__ . '/../files/github_credentials/config');
        $this->ssh->setKnownHostsFile(__DIR__ . '/../files/github_credentials/known_hosts');
    }

    /**
     * Get the ssh object.
     *
     * It manages the ssh connection config, so if you want to change it,
     * for example to set the identify file,
     * you need to retrieve and call its methods.
     * @api
     * @return ssh\Ssh
     */
    public function getSsh() : ssh\Ssh {
        return $this->ssh;
    }

    /**
     * Set the github user
     * @api
     * @param string $user
     * @return Github
     */
    public function setTargetUser(string $user) : Github {
        $this->target_user = $user;
        return $this;
    }

    /**
     * Return the clone command that will we used for the commit.
     *
     * You need to set the user, the repository name and the repository branch.
     * @internal This method is used for debug or testing purposes.
     * @param string $folder_name The target folder where the repository is cloned
     * @return string
     */
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
     * Execution of the deploy. If you don't call this then nothing is done.
     *
     * This clone the target repo in a temp folder, then rsync with the source dir and finally commit back to origin.
     * You can do a test run with test param on true. Fails when no change is done.
     * The temp folder is always deleted
     * @api
     * @param bool $test To just do a dry-run, the original repo is not changed
     * @uses Github::getCloneCommand() for the repository cloning
     * @uses Github::getCopyCommand() for the rsync copy
     * @return string The stdout of the internal commands, generally shows the Rsync output
     * @throws exception\GitCommandException
     * @throws exception\RsyncException
     * @throws ssh\exception\InvalidConfigFileException
     * @throws ssh\exception\InvalidIdentityFileException
     * @throws ssh\exception\InvalidKnownHostsFile
     * @throws \edwrodrig\deployer\util\exception\TempFolderCreationException
     */
    public function execute(bool $test = false) : string {
        /** @noinspection PhpInternalEntityUsedInspection */
        $folder_name = Util::createTempFolder();

        try {
            $std_out = '';

            $env = [
                'GIT_SSH_COMMAND' => $this->ssh->getCommand(),
                'EMAIL' => $this->email
            ];

            $command = $this->getCloneCommand($folder_name);
            $std_out .= self::runGitCommand($command, $folder_name, $env);

            $command = $this->getCopyCommand($folder_name);
            $std_out .= Rsync::runRsyncCommand($command);

            $std_out .= self::runGitCommand('git add -A', $folder_name, $env);

            $std_out .= self::runGitCommand(
                sprintf('git commit -m "%s"', $this->commit_message),
                $folder_name, $env
            );

            $std_out .= self::runGitCommand(
                sprintf('git push origin %s%s', $this->target_repo_branch, $test ? ' --dry-run' : ''),
                $folder_name, $env
            );


            return $std_out;
        } catch (   exception\RsyncException |
                    ssh\exception\InvalidConfigFileException |
                    ssh\exception\InvalidIdentityFileException |
                    ssh\exception\InvalidKnownHostsFile |
                    exception\GitCommandException $e ) {
            throw $e;
        } finally {
            exec(sprintf('rm -rf %s', $folder_name));
        }
    }

    /**
     * Utility method to run a git command.
     *
     * Just transform git errors to a GitCommandException with more clear information.
     * @internal It's used internally by some clases in this library to run git commands.
     * @param string $command The command to run (ej: git add -A)
     * @param string $current_working_dir current working dir of the command
     * @param array $env Environment variables as an key value array
     * @return string The standard output of the command
     * @throws exception\GitCommandException At failure
     */
    public static function runGitCommand(string $command, string $current_working_dir, array $env) : string {

        /** @noinspection PhpInternalEntityUsedInspection */
        if ( $result = Util::runCommand($command, $current_working_dir, $env) ) {

            if ( $result->getExitCode() == 0 ) {
                return $result->getStdOut();
            } else {
                /** @noinspection PhpInternalEntityUsedInspection */
                throw new exception\GitCommandException($result->getExitCode(), $result->getStdErrOrOut());
            }

        } else {
            /** @noinspection PhpInternalEntityUsedInspection */
            throw new exception\GitCommandException(255, 'proc_open fail');
        }
    }

    /**
     * Returns the rsync copy command.
     *
     * The params usend in the command are the following
     * ```
     * -r recurse into directories
     * -p preserve permissions
     * -t preserve modification times
     * -v verbose
     * -z compress
     * -L transform symlink into referent file/dir
     * -c skip based on checksum, not mod-time & size
     * --delete delete extraneous files from dest dirs
     * --progress show progress during transfer
     * --exclude=.git*
     * ```
     * @internal This method is used for debug or testing purposes.
     * @param string $target
     * @return string
     */
    public function getCopyCommand(string $target) {
        return sprintf('rsync -rptvzL %s %s --exclude=.git* --progress --delete',
            $this->source_dir . '/.',
            $target
        );
    }

    /**
     * The target github repository name
     * @api
     * @param string $target_repo_name
     * @return $this
     */
    public function setTargetRepoName(string $target_repo_name): Github
    {
        $this->target_repo_name = $target_repo_name;
        return $this;
    }

    /**
     * The target github repository branch. In github pages it used to be master or gh_pages
     * @api
     * @param string $target_repo_branch
     * @return $this
     */
    public function setTargetRepoBranch(string $target_repo_branch): Github
    {
        $this->target_repo_branch = $target_repo_branch;
        return $this;
    }

    /**
     * The source dir to commit.
     *
     * This directory is copied to the repo and then committed when {@see Github::execute() execute} is called.
     * Don't use trailing / in the dir name
     * @api
     * @param string $source_dir
     * @return $this
     */
    public function setSourceDir(string $source_dir): Github
    {
        $this->source_dir = $source_dir;
        return $this;
    }

}


