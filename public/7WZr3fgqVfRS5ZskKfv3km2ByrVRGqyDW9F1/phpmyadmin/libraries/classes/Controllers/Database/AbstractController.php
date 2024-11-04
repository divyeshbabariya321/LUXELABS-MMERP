<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Database;

use PhpMyAdmin\Template;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Controllers\AbstractController as Controller;

abstract class AbstractController extends Controller
{
    /** @var string */
    protected $db;

    public function __construct(ResponseRenderer $response, Template $template, string $db)
    {
        parent::__construct($response, $template);
        $this->db = $db;
    }
}
