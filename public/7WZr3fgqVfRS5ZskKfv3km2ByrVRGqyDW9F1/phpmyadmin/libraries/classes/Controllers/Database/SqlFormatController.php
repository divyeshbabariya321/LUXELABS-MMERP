<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Database;

use function strlen;
use PhpMyAdmin\SqlParser\Utils\Formatter;

/**
 * Format SQL for SQL editors.
 */
class SqlFormatController extends AbstractController
{
    public function __invoke(): void
    {
        $params = ['sql' => $_POST['sql'] ?? null];
        $query  = strlen((string) $params['sql']) > 0 ? $params['sql'] : '';
        $this->response->addJSON(['sql' => Formatter::format($query)]);
    }
}
