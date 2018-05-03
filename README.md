edwrodrig\deployer 
========
Library to deploy folders to __github__ or remotes using __rsync__

[![Latest Stable Version](https://poser.pugx.org/edwrodrig/deployer/v/stable)](https://packagist.org/packages/edwrodrig/deployer)
[![Total Downloads](https://poser.pugx.org/edwrodrig/deployer/downloads)](https://packagist.org/packages/edwrodrig/deployer)
[![License](https://poser.pugx.org/edwrodrig/deployer/license)](https://packagist.org/packages/edwrodrig/deployer)

## My use cases

Deploy static generated pages to:
 * a github repository used as a [github pages](https://pages.github.com/) host.
 * a remote folder using __rsync__ through __ssh__.
 
All transfer are secured by ssh with proper 
**[host key fingerprints](https://superuser.com/questions/421997/what-is-a-ssh-key-fingerprint-and-how-is-it-generated)** and
**[private keys](https://unix.stackexchange.com/questions/23291/how-to-ssh-to-remote-server-using-a-private-key)**.
If you don't know what are these or why are important go to read about it now.

## Documentation
The source code is documented using [phpDocumentor](http://docs.phpdoc.org/references/phpdoc/basic-syntax.html) style,
so it should pop up nicely if you're using IDEs like [PhpStorm](https://www.jetbrains.com/phpstorm) or similar.

### Examples

* [Github deploy](https://github.com/edwrodrig/deployer/blob/master/examples/github_deploy.php)

## Composer
```
composer require edwrodrig/deployer
```

## Testing
This library uses __phpunit__, but you need to run a virtual machine with a dummy host. The dummy host is a [TinyCoreLinux](http://distro.ibiblio.org/tinycorelinux/) with __ssh__, __rsync__ and __git__.
The virtual machine is provided as a [VirtualBox](https://www.virtualbox.org) appliance in [`files/DeployerTextTarget.ova`](https://github.com/edwrodrig/deployer/tree/master/files). The virtual machine must be running while the test are running.

## License
MIT license. Use it as you want at your own risk.











