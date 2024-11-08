<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table;

use PhpMyAdmin\Template;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Controllers\AbstractController as Controller;

abstract class AbstractController extends Controller
{
    /** @var string */
    protected $db;

    /** @var string */
    protected $table;

    public function __construct(ResponseRenderer $response, Template $template, string $db, string $table)
    {
        parent::__construct($response, $template);
        $this->db    = $db;
        $this->table = $table;
    }
}
