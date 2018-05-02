<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 01-05-18
 * Time: 16:34
 */

namespace tests\edwrodrig\deployer\ssh;

use edwrodrig\deployer\ssh\Ssh;
use PHPUnit\Framework\TestCase;

class SshTest extends TestCase
{
    public static function setCorrectFiles(Ssh $ssh) : Ssh {
        chmod(__DIR__ . '/../files/correct/id_rsa', 0600);
        $ssh->setConfigFile(__DIR__ . '/../files/correct/config');
        $ssh->setIdentityFile(__DIR__ . '/../files/correct/id_rsa');
        $ssh->setKnownHostsFile(__DIR__ . '/../files/correct/known_hosts');
        return $ssh;
    }

    public static function setWrongIdentity(Ssh $ssh) : Ssh {
        self::setCorrectFiles($ssh);
        chmod(__DIR__ . '/../files/wrong/id_rsa', 0600);
        $ssh->setIdentityFile(__DIR__ . '/../files/wrong/id_rsa');
        return $ssh;
    }

    /**
     * @throws \edwrodrig\deployer\ssh\exception\InvalidConfigFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidIdentityFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidKnownHostsFile
     */
    public function testHappyCase() {
        $ssh = self::setCorrectFiles(new Ssh);
        $this->assertStringStartsWith('ssh', $ssh->getCommand());
    }

    /**
     * @expectedException \edwrodrig\deployer\ssh\exception\InvalidConfigFileException
     * @expectedExceptionMessage /unexistant123123/config
     * @throws \edwrodrig\deployer\ssh\exception\InvalidConfigFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidIdentityFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidKnownHostsFile
     */
    public function testUnexistantConfigFile() {
        $ssh = self::setCorrectFiles(new Ssh);
        $ssh->setConfigFile('/unexistant123123/config');
        $this->assertStringStartsWith('ssh', $ssh->getCommand());
    }

    /**
     * @expectedException \edwrodrig\deployer\ssh\exception\InvalidIdentityFileException
     * @expectedExceptionMessage /unexistant123123/correct/id_rsa
     * @throws \edwrodrig\deployer\ssh\exception\InvalidConfigFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidIdentityFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidKnownHostsFile
     */
    public function testUnexistantIdentityFile() {
        $ssh = self::setCorrectFiles(new Ssh);
        $ssh->setIdentityFile('/unexistant123123/correct/id_rsa');
        $this->assertStringStartsWith('ssh', $ssh->getCommand());
    }

    /**
     * @expectedException \edwrodrig\deployer\ssh\exception\InvalidKnownHostsFile
     * @expectedExceptionMessage /unexistant123123/correct/known_hosts
     * @throws \edwrodrig\deployer\ssh\exception\InvalidConfigFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidIdentityFileException
     * @throws \edwrodrig\deployer\ssh\exception\InvalidKnownHostsFile
     */
    public function testUnexistantKnownHostsFile() {
        $ssh = self::setCorrectFiles(new Ssh);
        $ssh->setKnownHostsFile('/unexistant123123/correct/known_hosts');
        $this->assertStringStartsWith('ssh', $ssh->getCommand());
    }

}
