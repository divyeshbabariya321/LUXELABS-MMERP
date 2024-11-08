<?php

declare(strict_types=1);

namespace PhpMyAdmin\Twig;

use PhpMyAdmin\Table;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class TableExtension extends AbstractExtension
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
                'table_get',
                [Table::class, 'get']
            ),
        ];
    }
}
