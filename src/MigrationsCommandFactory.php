<?php

declare(strict_types=1);

namespace PsrContainerDoctrine;

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Psr\Container\ContainerInterface;

class MigrationsCommandFactory
{
    /**
     * @throws Exception\DomainException
     */
    public function __invoke(ContainerInterface $container, string $requestedName): MigrateCommand
    {
        /** @var DependencyFactory $factory */
        $factory = $container->get(DependencyFactory::class);

        return new MigrateCommand($factory);
    }
}
