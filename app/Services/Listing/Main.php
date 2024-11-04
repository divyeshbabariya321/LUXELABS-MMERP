<?php

namespace App\Services\Listing;

use App\Product;

class Main
{
    private $descriptionChecker;

    /**
     * @var SizesChecker
     */
    private $sizeChecker;

    private $product;

    /**
     * Main constructor.
     */
    public function __construct(private NameChecker $nameChecker, private CompositionChecker $compositionChecker, private ColorChecker $colorChecker, SizesChecker $sizesChecker, ShortDescriptionChecker $shortDescriptionChecker)
    {
        $this->descriptionChecker = $shortDescriptionChecker;
        $this->sizeChecker        = $sizesChecker;
    }

    public function validate(Product $product): bool
    {
        $this->product    = $product;
        $composition      = $this->isCompositionCorrect();
        $name             = $this->isNameCorrect();
        $shortDescription = $this->isShortDescriptionCorrect();
        $measurement      = $this->areMeasurementsCorrect();
        $sizes            = $this->isSizeCorrect();

        $status = $composition &&
            $name &&
            $shortDescription &&
            ($measurement || $sizes);

        return $status;
    }

    public function isSizeCorrect(): bool
    {
        $size = $this->sizeChecker->check($this->product);

        return $size;
    }

    public function isColorCorrect(): bool
    {
        $color = $this->colorChecker->check($this->product);

        return $color;
    }

    public function isCompositionCorrect(): bool
    {
        $composition = $this->compositionChecker->check($this->product);

        return $composition;
    }

    public function isShortDescriptionCorrect(): bool
    {
        $description = $this->descriptionChecker->check($this->product);

        return $description;
    }

    public function isNameCorrect(): bool
    {
        $name = $this->nameChecker->check($this->product);

        return $name;
    }

    public function areMeasurementsCorrect(): bool
    {
        $meas = $this->product->lmeasurement && $this->product->hmeasurement && $this->product->dmeasurement;

        return $meas;
    }
}
