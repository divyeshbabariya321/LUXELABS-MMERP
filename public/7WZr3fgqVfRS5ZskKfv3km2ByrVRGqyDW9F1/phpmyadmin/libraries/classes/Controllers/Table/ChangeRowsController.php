<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table;

use function __;
use PhpMyAdmin\Url;
use function is_array;
use PhpMyAdmin\Template;
use PhpMyAdmin\ResponseRenderer;

final class ChangeRowsController extends AbstractController
{
    /** @var ChangeController */
    private $changeController;

    public function __construct(
        ResponseRenderer $response,
        Template $template,
        string $db,
        string $table,
        ChangeController $changeController
    ) {
        parent::__construct($response, $template, $db, $table);
        $this->changeController = $changeController;
    }

    public function __invoke(): void
    {
        global $active_page, $where_clause;

        if (isset($_POST['goto']) && (! isset($_POST['rows_to_delete']) || ! is_array($_POST['rows_to_delete']))) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No row selected.'));

            return;
        }

        // As we got the rows to be edited from the
        // 'rows_to_delete' checkbox, we use the index of it as the
        // indicating WHERE clause. Then we build the array which is used
        // for the /table/change script.
        $where_clause = [];
        if (isset($_POST['rows_to_delete']) && is_array($_POST['rows_to_delete'])) {
            foreach ($_POST['rows_to_delete'] as $i_where_clause) {
                $where_clause[] = $i_where_clause;
            }
        }

        $active_page = Url::getFromRoute('/table/change');

        ($this->changeController)();
    }
}
