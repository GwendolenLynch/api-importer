<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\Fixtures;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class Fixture
{
    public static function dataFilePath(string $filename): string
    {
        $fs = new Filesystem();
        $path = Path::join(__DIR__, 'data', $filename);
        if (!$fs->exists($path)) {
            throw new IOException(sprintf('Data file not found at %s', $path));
        }

        return $path;
    }
}
