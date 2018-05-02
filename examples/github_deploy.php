<?php

include __DIR__ . '/../vendor/autoload.php';

$github = new \edwrodrig\deployer\Github;

$github->getSsh()->setIdentityFile(__DIR__ . '/id_rsa'); //you need to create identity file with ssh-keygen and register in github as a deploy key
$github->setTargetUser('edwrodrig');
$github->setTargetRepoName('test.git');
$github->setTargetRepoBranch('master');

$github->setSourceDir(__DIR__ . '/../src');
echo $github->execute(true); // true means dry run
