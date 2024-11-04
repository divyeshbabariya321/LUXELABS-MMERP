<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Server\Status;

use PhpMyAdmin\Template;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Server\Status\Data;
use PhpMyAdmin\Controllers\AbstractController as Controller;

abstract class AbstractController extends Controller
{
    /** @var Data */
    protected $data;

    public function __construct(ResponseRenderer $response, Template $template, Data $data)
    {
        parent::__construct($response, $template);
        $this->data = $data;
    }
}
