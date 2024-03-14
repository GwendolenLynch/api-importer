<?php

declare(strict_types=1);

namespace Camelot\ApiImporter;

use Camelot\ApiImporter\Repository\ImportJobRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

final class CamelotApiImporterBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(Reader\ReaderInterface::class)
            ->addTag('camelot.api_import.reader')
        ;
        $container->registerForAutoconfiguration(Handler\HandlerInterface::class)
            ->addTag('camelot.api_import.handler')
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $services = $containerConfigurator->services();
        $services->defaults()
            ->autoconfigure()
            ->autowire()
        ;

        $services->set(ImportJobRepository::class);

        $services->set(Reader\CsvReader::class);
        $services->set(Reader\TsvReader::class);

        $services->set(JobMonitor\BatchJobMonitor::class)
            ->share(false)
        ;
        $services->set(JobMonitor\ImportJobMonitor::class)
            ->share(false)
        ;

        $services->set(ImporterInterface::class);
        $services->set(DataImporter::class)
            ->arg('$importJobMonitor', service(JobMonitor\ImportJobMonitor::class))
            ->arg('$batchJobMonitor', service(JobMonitor\BatchJobMonitor::class))
            ->arg('$readers', tagged_iterator('camelot.api_import.reader'))
            ->arg('$handlers', tagged_iterator('camelot.api_import.handler'))
        ;
        $services->alias(ImporterInterface::class, DataImporter::class);
    }
}
