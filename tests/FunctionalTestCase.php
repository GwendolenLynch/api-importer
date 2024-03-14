<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests;

use Camelot\ApiImporter\JobMonitor;
use Camelot\ApiImporter\Tests\Fixtures\Command\CommandFixture;
use Camelot\ApiImporter\Tests\Fixtures\Handler\HandlerFixture;
use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class FunctionalTestCase extends KernelTestCase
{
    protected static function createKernel(array $options = []): KernelInterface
    {
        $env = $options['environment'] ?? $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'test';
        $debug = $options['debug'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? true;

        return new class($env, $debug) extends Kernel implements CompilerPassInterface {
            use MicroKernelTrait;

            public function process(ContainerBuilder $container): void
            {
                $container->getDefinition(JobMonitor\BatchJobMonitor::class)
                    ->setPublic(true)
                ;
                $container->getDefinition(JobMonitor\ImportJobMonitor::class)
                    ->setPublic(true)
                ;
            }

            public function registerBundles(): iterable
            {
                yield new FrameworkBundle();
                yield new DoctrineBundle();
                yield new DAMADoctrineTestBundle();
            }

            protected function prepareContainer(ContainerBuilder $container): void
            {
                parent::prepareContainer($container);

                $container->prependExtensionConfig('framework', ['test' => true]);

                $container->prependExtensionConfig('doctrine', [
                    'dbal' => [
                        'url' => 'sqlite:///%kernel.project_dir%/var/data.db',
                    ],
                    'orm' => [
                        'auto_generate_proxy_classes' => true,
                        'enable_lazy_ghost_objects' => true,
                        'report_fields_where_declared' => true,
                        'validate_xml_mapping' => true,
                        'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                        'auto_mapping' => true,
                        'mappings' => [
                            'Fixtures' => [
                                'type' => 'attribute',
                                'is_bundle' => false,
                                'dir' => '%kernel.project_dir%/tests/Fixtures/Entity',
                                'prefix' => 'Camelot\ApiImporter\Tests\Fixtures\Entity',
                                'alias' => 'Fixtures',
                            ],
                            'ImportJob' => [
                                'type' => 'attribute',
                                'is_bundle' => false,
                                'dir' => '%kernel.project_dir%/src/Entity',
                                'prefix' => 'Camelot\ApiImporter\Entity',
                                'alias' => 'ImportJob',
                            ],
                        ],
                    ],
                ]);
            }

            private function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
            {
                $container->services()->set(HandlerFixture::class)
                    ->tag('camelot.api_import.handler')
                ;

                $container->services()->set(CommandFixture::class)
                    ->autowire(true)
                    ->tag('console.command')
                ;
            }
        };
    }
}
