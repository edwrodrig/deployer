<?php

/**
 * This command generates a test ssh keypair
 */
passthru("ssh-keygen -t rsa -f ./id_rsa -N ''");

