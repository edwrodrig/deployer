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

}
