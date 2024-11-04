<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Server\Status\Monitor;

use PhpMyAdmin\Url;
use PhpMyAdmin\Template;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Server\Status\Data;
use PhpMyAdmin\Server\Status\Monitor;
use PhpMyAdmin\Controllers\Server\Status\AbstractController;

final class QueryAnalyzerController extends AbstractController
{
    /** @var Monitor */
    private $monitor;

    /** @var DatabaseInterface */
    private $dbi;

    public function __construct(
        ResponseRenderer $response,
        Template $template,
        Data $data,
        Monitor $monitor,
        DatabaseInterface $dbi
    ) {
        parent::__construct($response, $template, $data);
        $this->monitor = $monitor;
        $this->dbi     = $dbi;
    }

    public function __invoke(): void
    {
        global $errorUrl;

        $params = [
            'database' => $_POST['database'] ?? null,
            'query'    => $_POST['query'] ?? null,
        ];
        $errorUrl = Url::getFromRoute('/');

        if ($this->dbi->isSuperUser()) {
            $this->dbi->selectDb('mysql');
        }

        if (! $this->response->isAjax()) {
            return;
        }

        $this->response->addJSON([
            'message' => $this->monitor->getJsonForQueryAnalyzer(
                $params['database'] ?? '',
                $params['query'] ?? ''
            ),
        ]);
    }
}
