<?php

declare(strict_types=1);

namespace PhpMyAdmin\ConfigStorage\Features;

use PhpMyAdmin\Dbal\TableName;
use PhpMyAdmin\Dbal\DatabaseName;

/**
 * @psalm-immutable
 */
final class SavedQueryByExampleSearchesFeature
{
    /** @var DatabaseName */
    public $database;

    /** @var TableName */
    public $savedSearches;

    public function __construct(DatabaseName $database, TableName $savedSearches)
    {
        $this->database      = $database;
        $this->savedSearches = $savedSearches;
    }
}
