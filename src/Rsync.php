<?php
namespace edwrodrig\deployer;

class Rsync {

public $user;
public $server;
public $target;
public $source = 'output';

public function account() {
  return sprintf("%s@%s", $this->user, $this->server);
}

public function __invoke() {
  echo "DEPLOYING using RSYNC\n";
  echo "Uploading files...\n";
  $account = $this->account();
  Utils::call(sprintf('rsync -rLptgoDvzc -e "ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null" --progress %s %s:%s/live --delete', $this->source, $account, $this->target), "Error uploading output [$this->source] to [$account:$this->target]");
  echo "SITE DEPLOYED\n";

}

};


