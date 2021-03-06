# psr-container-doctrine: Doctrine Factories for PSR-11 Containers

## Installation

The easiest way to install this package is through composer:

```bash
$ composer require farums/psr-container-doctrine
```

## Configuration

In the general case where you are only using a single connection, it's enough to define the entity manager factory:

```php
return [
    'dependencies' => [
        'factories' => [
            'doctrine.entity_manager.orm_default' => \PsrContainerDoctrine\EntityManagerFactory::class,
        ],
    ],
];
```

If you want to add a second connection, or use another name than "orm_default", you can do so by using the static
variants of the factories:

```php
return [
    'dependencies' => [
        'factories' => [
            'doctrine.entity_manager.orm_other' => [\PsrContainerDoctrine\EntityManagerFactory::class, 'orm_other'],
        ],
    ],
];
```

You can also define an alias to retrieve an entity manager instance using `::class` capability:
```php
return [
    'aliases' => [
        'doctrine.entity_manager.orm_default' => Doctrine\ORM\EntityManagerInterface::class,
    ],
];
```

Each factory supplied by this package will by default look for a registered factory in the container. If it cannot find
one, it will automatically pull its dependencies from on-the-fly created factories. This saves you the hassle of
registering factories in your container which you may not need at all. Of course, you can always register those
factories when required. The following additional factories are available:

- ```\PsrContainerDoctrine\CacheFactory``` (doctrine.cache.*)
- ```\PsrContainerDoctrine\ConnectionFactory``` (doctrine.connection.*)
- ```\PsrContainerDoctrine\ConfigurationFactory``` (doctrine.configuration.*)
- ```\PsrContainerDoctrine\DriverFactory``` (doctrine.driver.*)
- ```\PsrContainerDoctrine\EventManagerFactory``` (doctrine.event_manager.*)
- ```\PsrContainerDoctrine\MigrationsConfigurationFactory``` (doctrine.migrations.*)

Each of those factories supports the same static behavior as the entity manager factory. For container specific
configurations, there are a few examples provided in the example directory:

- [Aura.Di](example/aura-di.php)
- [PimpleInterop](example/pimple-interop.php)
- [Laminas\ServiceManager](example/laminas-servicemanager.php)

## Example configuration

A complete example configuration can be found in [example/full-config.php](example/full-config.php). Please note that
the values in there are the defaults, and don't have to be supplied when you are not changing them. Keep your own
configuration as minimal as possible. A minimal configuration can be found in
[example/minimal-config.php](example/minimal-config.php)

## Migrations

If you want to expose the migration commands, you have to map the command name to `MigrationsCommandFactory`.  This factory needs migrations config setup.
For `ExecuteCommand` example:

```php
return [
    'dependencies' => [
        'factories' => [
            \Doctrine\Migrations\Tools\Console\Command\ExecuteCommand::class => \PsrContainerDoctrine\MigrationsCommandFactory::class,
        ],
    ],
];
```

You can find a full list of available commands in [example/full-config.php](example/full-config.php).

## Using the Doctrine CLI

In order to be able to use the CLI tool of Doctrine, you need to set-up a ```cli-config.php``` file in your project
directory. That file is generally quite short, and should look something like this for you:

```php
<?php
$container = require 'config/container.php';

return new \Symfony\Component\Console\Helper\HelperSet([
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper(
        $container->get('doctrine.entity_manager.orm_default')
    ),
]);
```

After that, you can simply invoke ```php vendor/bin/doctrine```. It gets a little trickier when you have multiple entity
managers. Doctrine itself has no way to handle that itself, so a possible way would be to have two separate directories
with two unique ```cli-config.php``` files. You then invoke the doctrine CLI from each respective directory. Since the
CLI is looking for the config file in the current working directory, it will then always use the one from the directory
you are currently in.
