<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 30-04-18
 * Time: 12:28
 */

namespace tests\edwrodrig\deployer;

use edwrodrig\deployer\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    /**
     * @expectedException \edwrodrig\deployer\exception\InvalidHostException
     * @expectedExceptionMessage
     * @testWith    ["", ""]
     */
    public function testWrongHost($user, $host) {
        new Account($user, $host);
    }

    /**
     * @throws \edwrodrig\deployer\exception\InvalidHostException
     * @testWith    ["edwin", "localhost", "edwin@localhost"]
     */
    public function testConstruct($user, $host, $expected) {
        $a = new Account($user, $host);
        $this->assertEquals($expected, strval($a));
    }

    /**
     * @throws \edwrodrig\deployer\exception\InvalidHostException
     */
    public function testSetters() {
        $a = new Account('edwin', 'localhost');
        $this->assertEquals('edwin@localhost', strval($a));
        $a->setHost('remotehost');
        $this->assertEquals('edwin@remotehost', strval($a));
        $a->setUser('edgar');
        $this->assertEquals('edgar@remotehost', strval($a));
    }
}
