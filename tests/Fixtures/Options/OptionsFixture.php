<?php

declare(strict_types=1);

namespace Camelot\ApiImporter\Tests\Fixtures\Options;

use Camelot\ApiImporter\Options\OptionsInterface;
use Camelot\ApiImporter\Options\OptionsTrait;
use Camelot\ApiImporter\Tests\Fixtures\Entity\EntityFixture;

final class OptionsFixture implements OptionsInterface
{
    use OptionsTrait;

    public function __construct()
    {
        $this->fileType = 'csv';
        $this->filePathname = '';
        $this->entityClass = EntityFixture::class;
    }
}
