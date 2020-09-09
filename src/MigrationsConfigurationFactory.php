<?php

declare(strict_types=1);

namespace PsrContainerDoctrine;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

/**
 * @method Configuration __invoke(ContainerInterface $container)
 */
class MigrationsConfigurationFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    protected function createWithConfig(ContainerInterface $container, string $configKey)
    {
        $migrationsConfig = $container->get($container, $configKey, 'migrations');

        $entityManager = $container->get(EntityManagerInterface::class);

        $configuration = new Configuration();
        $configuration->addMigrationsDirectory($migrationsConfig['namespace'], $migrationsConfig['directory']);
        $configuration->setAllOrNothing(true);
        $configuration->setCheckDatabasePlatform(false);

        $storageConfiguration = new TableMetadataStorageConfiguration();
        $storageConfiguration->setTableName($migrationsConfig['table']);

        $configuration->setMetadataStorageConfiguration($storageConfiguration);

        return DependencyFactory::fromEntityManager(
            new ExistingConfiguration($configuration),
            new ExistingEntityManager($entityManager)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig(string $configKey): array
    {
        return [
            'directory' => 'scripts/doctrine-orm-migrations',
            'name' => 'Doctrine Database Migrations',
            'namespace' => 'Elmut\Infrastructure\Doctrine\Migration',
            'table' => 'migrations',
            'column' => 'version',
        ];
    }
}
