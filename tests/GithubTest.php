<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 29-04-18
 * Time: 21:00
 */

namespace tests\edwrodrig\deployer;

use edwrodrig\deployer\Github;
use PHPUnit\Framework\TestCase;

class GithubTest extends TestCase
{
    /**
     * @throws \edwrodrig\deployer\exception\GitCommandException
     * @throws \edwrodrig\deployer\exception\RsyncException
     * @throws \edwrodrig\deployer\exception\TempFolderCreationException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidConfigFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidIdentityFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidKnownHostsFile
     */
    public function testHappy() {
        sleep(1);
        $r = new Github;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->setTargetUser('.');
        $r->setTargetRepoName('repo');
        $r->setTargetRepoBranch('master');
        $r->setSourceDir(__DIR__ . '/files/correct');
        $return = $r->execute(true);
        $this->assertStringStartsWith("sending incremental file list", $return);
    }

    /**
     * @throws \edwrodrig\deployer\ssh\exception\InvalidConfigFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidIdentityFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidKnownHostsFile
     * @expectedException \edwrodrig\deployer\exception\GitCommandException
     * @expectedExceptionMessage nothing to commit, working directory clean
     */
    public function testNothingToCommit() {
        sleep(1);
        $r = new Github;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->setTargetUser('.');
        $r->setTargetRepoName('repo');
        $r->setTargetRepoBranch('master');
        $r->setSourceDir(__DIR__ . '/files/repo_clone');
        $r->execute(true);
    }

    /**
     * @throws \edwrodrig\deployer\ssh\exception\InvalidConfigFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidIdentityFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidKnownHostsFile
     * @expectedException \edwrodrig\deployer\exception\GitCommandException
     * @expectedExceptionMessage Permission denied (publickey,password,keyboard-interactive).
     */
    public function testWrongIdentity() {
        sleep(1);
        $r = new Github;
        ssh\SshTest::setWrongIdentity($r->getSsh());

        $r->setTargetUser('.');
        $r->setTargetRepoName('repo');
        $r->setTargetRepoBranch('master');
        $r->setSourceDir(__DIR__ . '/files/correct');
        $r->execute(true);
    }

    /**
     * @throws \edwrodrig\deployer\ssh\exception\InvalidConfigFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidIdentityFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidKnownHostsFile
     * @expectedException \edwrodrig\deployer\exception\GitCommandException
     * @expectedExceptionMessage Host key verification failed.
     */
    public function testWrongKnownHosts() {
        sleep(1);
        $r = new Github;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->getSsh()->setKnownHostsFile(__DIR__ . '/files/wrong/known_hosts');
        $r->setTargetUser('.');
        $r->setTargetRepoName('repo');
        $r->setTargetRepoBranch('master');
        $r->setSourceDir(__DIR__ . '/files/correct');
        $r->execute(true);
    }

    /**
     * @throws \edwrodrig\deployer\ssh\exception\InvalidConfigFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidIdentityFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidKnownHostsFile
     * @expectedException \edwrodrig\deployer\exception\GitCommandException
     * @expectedExceptionMessage ssh: Could not resolve hostname github: Name or service not known
     */
    public function testWrongConfigFile() {
        sleep(1);
        $r = new Github;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->getSsh()->setConfigFile(__DIR__ . '/files/wrong/config');
        $r->setTargetUser('.');
        $r->setTargetRepoName('repo');
        $r->setTargetRepoBranch('master');
        $r->setSourceDir(__DIR__ . '/files/correct');
        $r->execute(true);
    }


}
