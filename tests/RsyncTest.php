<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 29-04-18
 * Time: 21:00
 */

use edwrodrig\deployer\Rsync;
use PHPUnit\Framework\TestCase;

class RsyncTest extends TestCase
{

    /**
     * @expectedException \edwrodrig\deployer\exception\RsyncException
     * @expectedExceptionMessage Executable not found
     * @expectedExceptionCode 127
     *
     * @throws \edwrodrig\deployer\exception\RsyncException
     */
    public function test() {
        $r = new Rsync;
        $r->set_executable('test_rsync');
        $r->execute_command();
    }
}
