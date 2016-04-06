<!--
    tagline: Script are callbacks that are called before/after installing packages
-->

# Scripts

## What is a script?

A script, in Composer's terms, can either be a PHP callback (defined as a
static method) or any command-line executable command. Scripts are useful
for executing a package's custom code or package-specific commands during
the Composer execution process.

> **Note:** Only scripts defined in the root package's `composer.json` are
> executed. If a dependency of the root package specifies its own scripts,
> Composer does not execute those additional scripts.


## Event names

Composer fires the following named events during its execution process:

### Command Events

- **pre-install-cmd**: occurs before the `install` command is executed.
- **post-install-cmd**: occurs after the `install` command is executed.
- **pre-update-cmd**: occurs before the `update` command is executed.
- **post-update-cmd**: occurs after the `update` command is executed.
- **pre-status-cmd**: occurs before the `status` command is executed.
- **post-status-cmd**: occurs after the `status` command is executed.
- **pre-archive-cmd**: occurs before the `archive` command is executed.
- **post-archive-cmd**: occurs after the `archive` command is executed.
- **pre-autoload-dump**: occurs before the autoloader is dumped, either
  during `install`/`update`, or via the `dump-autoload` command.
- **post-autoload-dump**: occurs after the autoloader is dumped, either
  during `install`/`update`, or via the `dump-autoload` command.
- **post-root-package-install**: occurs after the root package has been
  installed, during the `create-project` command.
- **post-create-project-cmd**: occurs after the `create-project` command is
  executed.

### Installer Events

- **pre-dependencies-solving**: occurs before the dependencies are resolved.
- **post-dependencies-solving**: occurs after the dependencies are resolved.

### Package Events

- **pre-package-install**: occurs before a package is installed.
- **post-package-install**: occurs after a package is installed.
- **pre-package-update**: occurs before a package is updated.
- **post-package-update**: occurs after a package is updated.
- **pre-package-uninstall**: occurs before a package has been uninstalled.
- **post-package-uninstall**: occurs after a package has been uninstalled.

### Plugin Events

- **command**: occurs before any Composer Command is executed on the CLI. It
  provides you with access to the input and output objects of the program.
- **pre-file-download**: occurs before files are downloaded and allows
  you to manipulate the `RemoteFilesystem` object prior to downloading files
  based on the URL to be downloaded.

> **Note:** Composer makes no assumptions about the state of your dependencies
> prior to `install` or `update`. Therefore, you should not specify scripts
> that require Composer-managed dependencies in the `pre-update-cmd` or
> `pre-install-cmd` event hooks. If you need to execute scripts prior to
> `install` or `update` please make sure they are self-contained within your
> root package.

## Defining scripts

The root JSON object in `composer.json` should have a property called
`"scripts"`, which contains pairs of named events and each event's
corresponding scripts. An event's scripts can be defined as either as a string
(only for a single script) or an array (for single or multiple scripts.)

For any given event:

- Scripts execute in the order defined when their corresponding event is fired.
- An array of scripts wired to a single event can contain both PHP callbacks
and command-line executable commands.
- PHP classes containing defined callbacks must be autoloadable via Composer's
autoload functionality.

Script definition example:

```json
{
    "scripts": {
        "post-update-cmd": "MyVendor\\MyClass::postUpdate",
        "post-package-install": [
            "MyVendor\\MyClass::postPackageInstall"
        ],
        "post-install-cmd": [
            "MyVendor\\MyClass::warmCache",
            "phpunit -c app/"
        ],
        "post-create-project-cmd" : [
            "php -r \"copy('config/local-example.php', 'config/local.php');\""
        ]
    }
}
```

Using the previous definition example, here's the class `MyVendor\MyClass`
that might be used to execute the PHP callbacks:

```php
<?php

namespace MyVendor;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class MyClass
{
    public static function postUpdate(Event $event)
    {
        $composer = $event->getComposer();
        // do stuff
    }

    public static function postPackageInstall(PackageEvent $event)
    {
        $installedPackage = $event->getOperation()->getPackage();
        // do stuff
    }

    public static function warmCache(Event $event)
    {
        // make cache toasty
    }
}
```

When an event is fired, your PHP callback receives as first argument an
`Composer\EventDispatcher\Event` object. This object has a `getName()` method
that lets you retrieve event name.

Depending on the script types (see list above) you will get various event
subclasses containing various getters with relevant data and associated
objects:

- Base class: [`Composer\EventDispatcher\Event`](https://getcomposer.org/apidoc/master/Composer/EventDispatcher/Event.html)
- Command Events: [`Composer\Script\Event`](https://getcomposer.org/apidoc/master/Composer/Script/Event.html)
- Installer Events: [`Composer\Installer\InstallerEvent`](https://getcomposer.org/apidoc/master/Composer/Installer/InstallerEvent.html)
- Package Events: [`Composer\Installer\PackageEvent`](https://getcomposer.org/apidoc/master/Composer/Installer/PackageEvent.html)
- Plugin Events:
  - command: [`Composer\Plugin\CommandEvent`](https://getcomposer.org/apidoc/master/Composer/Plugin/CommandEvent.html)
  - pre-file-download: [`Composer\Plugin\PreFileDownloadEvent`](https://getcomposer.org/apidoc/master/Composer/Plugin/PreFileDownloadEvent.html)

## Running scripts manually

If you would like to run the scripts for an event manually, the syntax is:

```sh
composer run-script [--dev] [--no-dev] script
```

For example `composer run-script post-install-cmd` will run any
**post-install-cmd** scripts that have been defined.

You can also give additional arguments to the script handler by appending `--`
followed by the handler arguments. e.g.
`composer run-script post-install-cmd -- --check` will pass`--check` along to
the script handler. Those arguments are received as CLI arg by CLI handlers,
and can be retrieved as an array via `$event->getArguments()` by PHP handlers.

## Writing custom commands

If you add custom scripts that do not fit one of the predefined event name
above, you can either run them with run-script or also run them as native
Composer commands. For example the handler defined below is executable by
simply running `composer test`:

```json
{
    "scripts": {
        "test": "phpunit"
    }
}
```

> **Note:** Composer's bin-dir is pushed on top of the PATH so that binaries
> of dependencies are easily accessible as CLI commands when writing scripts.
