<?php
/**
 *  This script is an example of deploying to remote dir using rsync.
 */

use edwrodrig\deployer\exception\RsyncException;

include __DIR__ . '/../vendor/autoload.php';

$rsync = new \edwrodrig\deployer\Rsync;

$rsync
    ->setSourceDir(__DIR__ . '/../src')
    ->setTargetDir('.')
    ->getSsh()
        ->setConfigFile(__DIR__ . '/../tests/files/correct/config')
        ->setKnownHostsFile(__DIR__ . '/../tests/files/correct/known_hosts')
        ->setIdentityFile(__DIR__ . '/../tests/files/correct/id_rsa');

try {
    echo $rsync->execute(true); // true means dry run
} catch ( RsyncException $e ) {
    echo get_class($e) , "\n", $e->getMessage(), "\n", $e->getFullError(). "\n";
} catch ( Exception $e ) {
    echo get_class($e) , "\n", $e->getMessage(), "\n";
}
