<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Server;

use PhpMyAdmin\Url;
use PhpMyAdmin\Template;
use PhpMyAdmin\StorageEngine;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Controllers\AbstractController;

/**
 * Handles viewing storage engine details
 */
class EnginesController extends AbstractController
{
    /** @var DatabaseInterface */
    private $dbi;

    public function __construct(ResponseRenderer $response, Template $template, DatabaseInterface $dbi)
    {
        parent::__construct($response, $template);
        $this->dbi = $dbi;
    }

    public function __invoke(): void
    {
        global $errorUrl;

        $errorUrl = Url::getFromRoute('/');

        if ($this->dbi->isSuperUser()) {
            $this->dbi->selectDb('mysql');
        }

        $this->render('server/engines/index', [
            'engines' => StorageEngine::getStorageEngines(),
        ]);
    }
}
