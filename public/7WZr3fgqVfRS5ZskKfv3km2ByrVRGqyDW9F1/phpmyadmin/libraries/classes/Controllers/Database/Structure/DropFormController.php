<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Database\Structure;

use function __;
use PhpMyAdmin\Util;
use function in_array;
use PhpMyAdmin\Template;
use function htmlspecialchars;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Utils\ForeignKey;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Controllers\Database\AbstractController;

final class DropFormController extends AbstractController
{
    /** @var DatabaseInterface */
    private $dbi;

    public function __construct(ResponseRenderer $response, Template $template, string $db, DatabaseInterface $dbi)
    {
        parent::__construct($response, $template, $db);
        $this->dbi = $dbi;
    }

    public function __invoke(): void
    {
        global $db;

        $selected = $_POST['selected_tbl'] ?? [];

        if (empty($selected)) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No table selected.'));

            return;
        }

        $views = $this->dbi->getVirtualTables($db);

        $fullQueryViews = '';
        $fullQuery      = '';

        foreach ($selected as $selectedValue) {
            $current = $selectedValue;
            if (! empty($views) && in_array($current, $views)) {
                $fullQueryViews .= (empty($fullQueryViews) ? 'DROP VIEW ' : ', ')
                    . Util::backquote(htmlspecialchars($current));
            } else {
                $fullQuery .= (empty($fullQuery) ? 'DROP TABLE ' : ', ')
                    . Util::backquote(htmlspecialchars($current));
            }
        }

        if (! empty($fullQuery)) {
            $fullQuery .= ';<br>' . "\n";
        }

        if (! empty($fullQueryViews)) {
            $fullQuery .= $fullQueryViews . ';<br>' . "\n";
        }

        $urlParams = ['db' => $db];
        foreach ($selected as $selectedValue) {
            $urlParams['selected'][] = $selectedValue;
        }

        foreach ($views as $current) {
            $urlParams['views'][] = $current;
        }

        $this->render('database/structure/drop_form', [
            'url_params'           => $urlParams,
            'full_query'           => $fullQuery,
            'is_foreign_key_check' => ForeignKey::isCheckEnabled(),
        ]);
    }
}
