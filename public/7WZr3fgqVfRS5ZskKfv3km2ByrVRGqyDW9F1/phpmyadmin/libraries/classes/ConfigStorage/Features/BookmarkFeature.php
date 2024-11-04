<?php

declare(strict_types=1);

namespace PhpMyAdmin\ConfigStorage\Features;

use PhpMyAdmin\Dbal\TableName;
use PhpMyAdmin\Dbal\DatabaseName;

/**
 * @psalm-immutable
 */
final class BookmarkFeature
{
    /** @var DatabaseName */
    public $database;

    /** @var TableName */
    public $bookmark;

    public function __construct(DatabaseName $database, TableName $bookmark)
    {
        $this->database = $database;
        $this->bookmark = $bookmark;
    }
}
