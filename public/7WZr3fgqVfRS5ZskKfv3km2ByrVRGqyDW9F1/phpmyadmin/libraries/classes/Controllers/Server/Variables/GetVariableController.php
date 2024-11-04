<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Server\Variables;

use function header;
use PhpMyAdmin\Util;
use function implode;
use PhpMyAdmin\Template;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\Providers\ServerVariables\ServerVariablesProvider;

final class GetVariableController extends AbstractController
{
    /** @var DatabaseInterface */
    private $dbi;

    public function __construct(ResponseRenderer $response, Template $template, DatabaseInterface $dbi)
    {
        parent::__construct($response, $template);
        $this->dbi = $dbi;
    }

    /**
     * @param array $params Request parameters
     */
    public function __invoke(ServerRequest $request, array $params): void
    {
        if (! $this->response->isAjax()) {
            return;
        }

        // Send with correct charset
        header('Content-Type: text/html; charset=UTF-8');
        // Do not use double quotes inside the query to avoid a problem
        // when server is running in ANSI_QUOTES sql_mode
        $varValue = $this->dbi->fetchSingleRow(
            'SHOW GLOBAL VARIABLES WHERE Variable_name=\''
            . $this->dbi->escapeString($params['name']) . '\';',
            DatabaseInterface::FETCH_NUM
        );

        $json = [
            'message' => $varValue[1],
        ];

        $variableType = ServerVariablesProvider::getImplementation()->getVariableType($params['name']);

        if ($variableType === 'byte') {
            /** @var string[] $bytes */
            $bytes           = Util::formatByteDown($varValue[1], 3, 3);
            $json['message'] = implode(' ', $bytes);
        }

        $this->response->addJSON($json);
    }
}
