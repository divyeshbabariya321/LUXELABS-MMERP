<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers;

use PhpMyAdmin\Git;
use PhpMyAdmin\Util;
use PhpMyAdmin\Config;
use function strtotime;
use PhpMyAdmin\Template;
use PhpMyAdmin\ResponseRenderer;

final class GitInfoController extends AbstractController
{
    /** @var Config */
    private $config;

    public function __construct(ResponseRenderer $response, Template $template, Config $config)
    {
        parent::__construct($response, $template);
        $this->config = $config;
    }

    public function __invoke(): void
    {
        if (! $this->response->isAjax()) {
            return;
        }

        $git = new Git($this->config->get('ShowGitRevision') ?? true);

        if (! $git->isGitRevision()) {
            return;
        }

        $commit = $git->checkGitRevision();

        if (! $git->hasGitInformation() || $commit === null) {
            $this->response->setRequestStatus(false);

            return;
        }

        $commit['author']['date']    = Util::localisedDate(strtotime($commit['author']['date']));
        $commit['committer']['date'] = Util::localisedDate(strtotime($commit['committer']['date']));

        $this->render('home/git_info', $commit);
    }
}
