<?php

declare(strict_types=1);

namespace PhpMyAdmin\Twig;

use Twig\TwigFunction;
use PhpMyAdmin\Transformations;
use Twig\Extension\AbstractExtension;

class TransformationsExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        $transformations = new Transformations();

        return [
            new TwigFunction(
                'get_description',
                [$transformations, 'getDescription']
            ),
            new TwigFunction(
                'get_name',
                [$transformations, 'getName']
            ),
        ];
    }
}
