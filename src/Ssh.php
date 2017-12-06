<?php
namespace edwrodrig\deployer;

class Ssh {

public $user;
public $server;
public $target;
public $source = 'output';

public function account() {
  return sprintf("%s@%s", $this->user, $this->server);
}

public function __invoke() {
  echo "DEPLOYING throught SSH\n";
  echo "Uploading files...\n";
  $account = $this->account();
  Utils::call(sprintf('scp -r %s %s:%s/new', $this->source, $account, $this->target), "Error uploading output [$this->source] to [$account:$this->target]");
  echo "Updating folders\n";
  Utils::call(sprintf('ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null %s "cd %s; rm -rf old; mv live old; mv new live;"', $account, $this->target), "Error updating www folders , do it manually!!");
  echo "SITE DEPLOYED\n";

}

};


