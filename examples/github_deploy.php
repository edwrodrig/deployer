<?php
/**
 *  This script is an example of deploying to github. It is ideal for deploying github pages
 */

include __DIR__ . '/../vendor/autoload.php';


$github = new \edwrodrig\deployer\Github;

$github
    ->setTargetUser('edwrodrig')
    ->setTargetRepoName('test')
    ->setTargetRepoBranch('master')
    ->setSourceDir(__DIR__ . '/../src');

/*
 * you need to create identity file with ssh-keygen and register in github as a deploy key
 * you can generate example keys with generate_example_keys.php script in this same folder
 */
$github->getSsh()->setIdentityFile(__DIR__ . '/id_rsa');

try {
    echo $github->execute(true); // true means dry run
} catch ( Exception $e ) {
    echo get_class($e) , "\n", $e->getMessage(), "\n";
}
