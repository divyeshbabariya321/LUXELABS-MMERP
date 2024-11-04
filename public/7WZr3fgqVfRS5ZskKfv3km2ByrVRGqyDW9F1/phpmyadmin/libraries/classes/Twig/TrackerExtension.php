<?php

declare(strict_types=1);

namespace PhpMyAdmin\Twig;

use Twig\TwigFunction;
use PhpMyAdmin\Tracker;
use Twig\Extension\AbstractExtension;

class TrackerExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'get_tracker_version',
                [Tracker::class, 'getVersion']
            ),
        ];
    }
}
