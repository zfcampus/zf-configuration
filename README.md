ZF Configuration
================

[![Build Status](https://travis-ci.org/zfcampus/zf-configuration.png)](https://travis-ci.org/zfcampus/zf-configuration)

Introduction
------------

zf-configuration is a module that provides configuration services that provide for the
runtime management and modification of Zend Framework application configuration files.

Requirements
------------
  
Please see the [composer.json](composer.json) file.

Installation
------------

Run the following `composer` command:

```console
$ composer require zfcampus/zf-configuration
```

Alternately, manually add the following to your `composer.json`, in the `require` section:

```javascript
"require": {
    "zfcampus/zf-configuration": "^1.2"
}
```

And then run `composer update` to ensure the module is installed.

Finally, add the module name to your project's `config/application.config.php` under the `modules`
key:

```php
return [
    /* ... */
    'modules' => [
        /* ... */
        'ZF\Configuration',
    ],
    /* ... */
];
```

> ### zf-component-installer
>
> If you use [zf-component-installer](https://github.com/zendframework/zf-component-installer),
> that plugin will install zf-configuration as a module for you.

Configuration
-------------

### User Configuration

The top-level configuration key for user configuration of this module is `zf-configuration`.

```php
'zf-configuration' => [
    'config_file' => 'config/autoload/development.php',
    'enable_short_array' => true,
],
```

#### Key: `enable_short_array`

Set this value to a boolean `false` if you don't want to use square bracket (aka "short") array
syntax.

ZF2 Events
----------

There are no events or listeners.

ZF2 Services
------------

#### ZF\Configuration\ConfigWriter

`ZF\Configuration\ConfigWriter` is by default an instance of `Zend\Config\Writer\PhpArray`.  This
service serves the purpose of providing the necessary dependencies for `ConfigResource` and
`ConfigResourceFactory`.

#### ZF\Configuration\ConfigResource

`ZF\Configuration\ConfigResource` service is used for modifying an existing configuration files with
methods such as `patch()` and `replace()`.  The service returned by the service manager is bound to
the file specified in the `config_file` key.

#### ZF\Configuration\ConfigResourceFactory

`ZF\Configuration\ConfigResourceFactory` is a factory service that provides consumers with the
ability to create `ZF\Configuration\ConfigResource` objects, with dependencies injected for specific
config files (not the one listed in the `module.config.php`.

#### ZF\Configuration\ModuleUtils

`ZF\Configuration\ModuleUtils` is a service that consumes the `ModuleManager` and provides the
ability to traverse modules to find their path on disk as well as the path to their configuration
files.
