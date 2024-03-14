<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\Command;

use Camelot\ApiImporter\Command\AbstractImportCommand;
use Camelot\ApiImporter\Tests\Fixtures\Fixture;
use Camelot\ApiImporter\Tests\FunctionalTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/** @internal */
#[CoversClass(AbstractImportCommand::class)]
final class AbstractImportCommandTest extends FunctionalTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:import:test');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => Fixture::dataFilePath('two-line-headers.csv'),
            '--header-offset' => 0,
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();

        self::assertStringContainsString('Processed: 2', $output);
        self::assertStringContainsString('Created: 2', $output);
    }
}
