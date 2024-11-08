<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Sql;

use PhpMyAdmin\Template;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Utils\ForeignKey;
use PhpMyAdmin\CheckUserPrivileges;
use PhpMyAdmin\Controllers\AbstractController;

final class DefaultForeignKeyCheckValueController extends AbstractController
{
    /** @var CheckUserPrivileges */
    private $checkUserPrivileges;

    public function __construct(
        ResponseRenderer $response,
        Template $template,
        CheckUserPrivileges $checkUserPrivileges
    ) {
        parent::__construct($response, $template);
        $this->checkUserPrivileges = $checkUserPrivileges;
    }

    public function __invoke(): void
    {
        $this->checkUserPrivileges->getPrivileges();
        $this->response->addJSON('default_fk_check_value', ForeignKey::isCheckEnabled());
    }
}
