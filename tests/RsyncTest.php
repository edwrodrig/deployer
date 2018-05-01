<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 29-04-18
 * Time: 21:00
 */

namespace tests\edwrodrig\deployer;

use edwrodrig\deployer\Rsync;
use PHPUnit\Framework\TestCase;

class RsyncTest extends TestCase
{

    /**
     * @expectedException \edwrodrig\deployer\exception\RsyncException
     * @expectedExceptionMessage Executable not found
     * @expectedExceptionCode 127
     */
    public function testCommandNotExistant() {
        $r = new Rsync;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->setExecutable('test_not_existant_rsync');
        $this->assertFalse($r->doesExecutableExists());

        $this->assertStringStartsWith('test_not_existant_rsync', $r->getCommand());
        $r->execute();
    }

    public function testRsyncDoesExist() : Rsync {
        $r = new Rsync;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->setExecutable('rsync');
        $this->assertTrue($r->doesExecutableExists());
        return $r;
    }

    /**
     * @expectedException \edwrodrig\deployer\exception\RsyncException
     * @expectedExceptionCode 255
     * @expectedExceptionMessage ssh: Could not resolve hostname unknown_host: Name or service not known
     * @throws \edwrodrig\deployer\exception\RsyncException
     */
    public function testWrongHostName() {
        sleep(1);
        $r = new Rsync;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->getSsh()->setConfigFile(__DIR__ . '/files/wrong/config');
        $r->execute(true);
    }

    /**
     * @expectedException \edwrodrig\deployer\exception\RsyncException
     * @expectedExceptionCode 255
     * @expectedExceptionMessage Permission denied (publickey,password,keyboard-interactive).
     * @throws \edwrodrig\deployer\exception\RsyncException
     */
    public function testWrongIdentityFile() {
        $r = new Rsync;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->getSsh()->setIdentityFile(__DIR__ . '/files/wrong/id_rsa');
        $r->setSourceDir(__DIR__ .  '/files/correct');
        $r->execute(true);
    }

    /**
     * @expectedException \edwrodrig\deployer\exception\RsyncException
     * @expectedExceptionCode 255
     * @expectedExceptionMessage Host key verification failed.
     * @throws \edwrodrig\deployer\exception\RsyncException
     */
    public function testWrongKnownHostFile() {
        sleep(1);
        $r = new Rsync;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->getSsh()->setKnownHostsFile(__DIR__ . '/files/wrong/known_hosts');
        $r->setSourceDir(__DIR__ .  '/files/correct');
        $r->execute(true);
    }

    /**
     * @throws \edwrodrig\deployer\exception\RsyncException
     */
    public function testHappy() {
        sleep(1);
        $r = new Rsync;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->setSourceDir(__DIR__ .  '/files/correct');
        $return = $r->execute(true);
        $this->assertStringStartsWith("sending incremental file list", $return);
    }

    /**
     * @throws \edwrodrig\deployer\exception\RsyncException
     * @expectedException \edwrodrig\deployer\exception\RsyncException
     * @expectedExceptionCode 23
     * @expectedExceptionMessage Partial transfer due to error
     */
    public function testUnexistantFolder() {
        sleep(1);
        $r = new Rsync;
        ssh\SshTest::setCorrectFiles($r->getSsh());
        $r->setSourceDir('/unexistant/washulin');
        $return = $r->execute(true);
        $this->assertStringStartsWith("sending incremental file list", $return);
    }

}
