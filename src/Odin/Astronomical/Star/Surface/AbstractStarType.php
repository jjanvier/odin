<?php

declare(strict_types=1);

namespace Odin\Astronomical\Star\Surface;

use Odin\Drawer\Gd\ColorHelper;
use Odin\Drawer\Gd\LayerOrchestrator;

/**
 * Abstract representation of a Star Type.
 */
abstract class AbstractStarType implements StarTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(int $size)
    {
        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->initBaseLayer($size, $size, '#000', LayerOrchestrator::TRANSPARENT);
        $surface = $layerOrchestrator->render();

        $allocatedColors = $this->allocatePaletteColors($surface);
        
        // Draw the star as a filled circle with the core color
        imagefilledellipse(
            $surface, 
            $size / 2, 
            $size / 2, 
            $size, 
            $size, 
            $allocatedColors['core']
        );
        
        // Apply surface texture and effects specific to each star type
        $this->applySurfaceEffects($surface, $size, $allocatedColors);

        return $surface;
    }

    /**
     * Apply surface texture and effects specific to each star type.
     */
    abstract protected function applySurfaceEffects($surface, int $size, array $colors): void;

    /**
     * For the given $layer, it will return an array of allocated colors based
     * on the color palette of this star type.
     */
    private function allocatePaletteColors($layer): array
    {
        $allocatedColors = [];
        $palette = $this->getColorPalette();

        foreach ($palette as $colorName => $hexColor) {
            list($r, $g, $b) = ColorHelper::hexToRgb($hexColor);
            $allocatedColors[$colorName] = imagecolorallocate($layer, $r, $g, $b);
        }

        return $allocatedColors;
    }
}
