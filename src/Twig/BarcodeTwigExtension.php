<?php

namespace Survos\BarcodeBundle\Twig;

use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BarcodeTwigExtension extends AbstractExtension
{
    public function __construct(private int $widthFactor,
                                private int $height,
                                private string $foregroundColor)
    {

    }
    public function getFilters(): array
    {
        return [
            new TwigFilter('barcode', [$this, 'barcode'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('barcode', [$this, 'barcode'], ['is_safe' => ['html']]),
        ];
    }

    public function barcode(string $value, ?int $widthFactor = null, ?int $height = null, ?string $foregroundColor = null, string $type = BarcodeGenerator::TYPE_CODE_128): string
    {
        $generator = new BarcodeGeneratorSVG();
        return $generator->getBarcode($value, $type,
            $widthFactor ?? $this->widthFactor,
            $height ?? $this->height,
            $foregroundColor ?? $this->foregroundColor);
    }
}
