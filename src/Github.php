<?php
namespace edwrodrig\deployer;

class Github {

public $user;
public $target;
public $source = 'output';
public $branch = 'master';

public function __invoke() {
  echo "DEPLOYING to GITHUB\n";
  echo "Uploading files...\n";
  Utils::call('rm -rf /tmp/gitrepo', 'Error removing temporary folder');
  Utils::call(sprintf('git clone git@github.com:%s/%s.git /tmp/gitrepo', $this->user, $this->target), "Error cloning git repository $this->user@$this->target");
  Utils::call(sprintf('cd /tmp/gitrepo; git checkout %s', $this->branch), 'Fail to change branch');
  Utils::call("rm -rf /tmp/gitrepo/*; cp -rf $this->source/* /tmp/gitrepo/", 'Error preparing commit');
  Utils::call(sprintf('cd /tmp/gitrepo; git add -A; git commit -a -m "Automatic deploy"; git push origin %s', $this->branch), 'Error uploading to github');
  Utils::call('rm -rf /tmp/gitrepo', 'Error removing temporary file'); 
  echo "SITE DEPLOYED\n";

}

};


