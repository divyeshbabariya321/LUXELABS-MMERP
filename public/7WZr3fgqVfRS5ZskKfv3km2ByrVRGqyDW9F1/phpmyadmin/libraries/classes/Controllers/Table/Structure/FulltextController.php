<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table\Structure;

use function __;
use function count;
use PhpMyAdmin\Util;
use PhpMyAdmin\Message;
use PhpMyAdmin\Template;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Controllers\Table\AbstractController;
use PhpMyAdmin\Controllers\Table\StructureController;

final class FulltextController extends AbstractController
{
    /** @var DatabaseInterface */
    private $dbi;

    /** @var StructureController */
    private $structureController;

    public function __construct(
        ResponseRenderer $response,
        Template $template,
        string $db,
        string $table,
        DatabaseInterface $dbi,
        StructureController $structureController
    ) {
        parent::__construct($response, $template, $db, $table);
        $this->dbi                 = $dbi;
        $this->structureController = $structureController;
    }

    public function __invoke(): void
    {
        global $sql_query, $db, $table, $message;

        $selected = $_POST['selected_fld'] ?? [];

        if (empty($selected)) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No column selected.'));

            return;
        }

        $i             = 1;
        $selectedCount = count($selected);
        $sql_query     = 'ALTER TABLE ' . Util::backquote($table) . ' ADD FULLTEXT(';

        foreach ($selected as $field) {
            $sql_query .= Util::backquote($field);
            $sql_query .= $i++ === $selectedCount ? ');' : ', ';
        }

        $this->dbi->selectDb($db);
        $result = $this->dbi->tryQuery($sql_query);

        if (! $result) {
            $message = Message::error($this->dbi->getError());
        }

        if (empty($message)) {
            $message = Message::success();
        }

        ($this->structureController)();
    }
}
