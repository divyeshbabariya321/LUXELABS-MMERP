<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Database\Structure;

use function __;
use PhpMyAdmin\Controllers\Database\AbstractController;

final class CopyFormController extends AbstractController
{
    public function __invoke(): void
    {
        global $db, $dblist;

        $selected = $_POST['selected_tbl'] ?? [];

        if (empty($selected)) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No table selected.'));

            return;
        }

        $urlParams = ['db' => $db];
        foreach ($selected as $selectedValue) {
            $urlParams['selected'][] = $selectedValue;
        }

        $databasesList = $dblist->databases;
        foreach ($databasesList as $key => $databaseName) {
            if ($databaseName == $db) {
                $databasesList->offsetUnset($key);
                break;
            }
        }

        $this->response->disable();
        $this->render('database/structure/copy_form', [
            'url_params' => $urlParams,
            'options'    => $databasesList->getList(),
        ]);
    }
}
