<?php

declare(strict_types=1);

namespace Odin\Astronomical\Star;

use Odin\Astronomical\Star\Surface\StarSurfaceGeneratorRegistry;
use Odin\Astronomical\Star\Surface\StarTypeInterface;
use Odin\Drawer\Gd\GradientAlpha;
use Odin\Drawer\Gd\LayerOrchestrator;
use Odin\Drawer\Gd\Text;

class Star
{
    private $image;

    /** @var int */
    private $layerWidth;

    /** @var int */
    private $layerHeight;

    /** @var string */
    private $type;

    /** @var int */
    private $starSize;

    public function __construct(string $type, ?int $starSize = null)
    {
        $this->type = $type;

        if (null !== $starSize) {
            $this->starSize = $starSize;
        } else {
            $this->starSize = $this->makeEven(rand(150, 250));
        }

        $this->layerWidth = $this->starSize * 2;
        $this->layerHeight = $this->starSize * 2;
    }

    public function render()
    {
        $generatorRegistry = new StarSurfaceGeneratorRegistry();
        /** @var StarTypeInterface $surfaceGenerator */
        $surfaceGenerator = $generatorRegistry->forType($this->type);

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->initBaseLayer($this->layerWidth, $this->layerHeight, '#000', 127);
        
        // Add outer glow first (larger, more transparent)
        $outerGlowSize = $this->makeEven(round($this->starSize * 2.0));
        $layerOrchestrator->addLayer(
            $this->generateGlow(
                $surfaceGenerator->getColorPalette(), 
                $outerGlowSize, 
                0x22
            ), 
            ($this->layerWidth - $outerGlowSize) / 2, 
            ($this->layerHeight - $outerGlowSize) / 2
        );
        
        // Add middle glow (medium size, medium opacity)
        $middleGlowSize = $this->makeEven(round($this->starSize * 1.6));
        $layerOrchestrator->addLayer(
            $this->generateGlow(
                $surfaceGenerator->getColorPalette(), 
                $middleGlowSize, 
                0x44
            ), 
            ($this->layerWidth - $middleGlowSize) / 2, 
            ($this->layerHeight - $middleGlowSize) / 2
        );
        
        // Add inner glow (smaller, more intense)
        $innerGlowSize = $this->makeEven(round($this->starSize * 1.3));
        $layerOrchestrator->addLayer(
            $this->generateGlow(
                $surfaceGenerator->getColorPalette(), 
                $innerGlowSize, 
                0x88
            ), 
            ($this->layerWidth - $innerGlowSize) / 2, 
            ($this->layerHeight - $innerGlowSize) / 2
        );

        $starLayers = new LayerOrchestrator();
        $starLayers->initBaseLayer($this->layerWidth, $this->layerHeight, '#000', 127);
        $starLayer = $starLayers->render();

        // Generate surface
        $surface = $surfaceGenerator->generate($this->starSize);
        $x = ($this->layerWidth / 2) - ($this->starSize / 2);
        $y = ($this->layerHeight / 2) - ($this->starSize / 2);
        $starLayers->addLayer($surface, $x, $y);

        $layerOrchestrator->addLayer($starLayer);

        $image = $layerOrchestrator->render();
        
        // Add lens flare effect for more visual appeal
        $this->addLensFlareEffect($image, $surfaceGenerator->getColorPalette());
        
        // Add the star name with a more subtle appearance
        $this->addStarName($image, $surfaceGenerator->getName());

        $this->image = $image;

        return $image;
    }

    private function generateGlow(array $palette, int $size, int $intensity)
    {
        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->initBaseLayer($size, $size, '#000', 127);
        $layer = $layerOrchestrator->render();

        // Use the corona color for the glow if available, otherwise use core
        $glowColor = $palette['corona'] ?? $palette['core'];
        
        $glow = new GradientAlpha($size, $size, 'ellipse', $glowColor, 0x00, $intensity, 0);
        $layerOrchestrator->addLayer($glow->image, 0, 0);

        return $layer;
    }
    
    private function addLensFlareEffect($image, array $palette)
    {
        // Extract color components from the core color
        $coreColor = $palette['core'];
        list($r, $g, $b) = sscanf($coreColor, "#%02x%02x%02x");
        
        // Create small light streaks in 4-8 directions
        $numStreaks = rand(4, 8);
        $centerX = $this->layerWidth / 2;
        $centerY = $this->layerHeight / 2;
        $maxLength = $this->starSize * 0.7;
        
        for ($i = 0; $i < $numStreaks; $i++) {
            $angle = (2 * M_PI / $numStreaks) * $i;
            $length = $maxLength * (0.7 + (rand(0, 30) / 100));
            
            $endX = $centerX + cos($angle) * $length;
            $endY = $centerY + sin($angle) * $length;
            
            // Draw a line with decreasing opacity
            $steps = 20;
            for ($j = 0; $j < $steps; $j++) {
                $t = $j / $steps;
                $x = $centerX + ($endX - $centerX) * $t;
                $y = $centerY + ($endY - $centerY) * $t;
                
                // Decrease opacity as we move away from center
                $alpha = 100 * (1 - $t);
                $color = imagecolorallocatealpha($image, $r, $g, $b, (int)(127 - ($alpha * 127 / 100)));
                
                // Draw a small point
                $pointSize = 2 * (1 - $t);
                imagefilledellipse($image, (int)$x, (int)$y, (int)$pointSize, (int)$pointSize, $color);
            }
        }
        
        // Add a few small random glints
        for ($i = 0; $i < 5; $i++) {
            $distance = rand($this->starSize / 2, $this->starSize);
            $angle = rand(0, 360) * M_PI / 180;
            
            $x = $centerX + cos($angle) * $distance;
            $y = $centerY + sin($angle) * $distance;
            
            $glintSize = rand(2, 5);
            $glintColor = imagecolorallocatealpha($image, $r, $g, $b, rand(70, 100));
            
            imagefilledellipse($image, (int)$x, (int)$y, $glintSize, $glintSize, $glintColor);
        }
    }
    
    private function addStarName($image, string $name)
    {
        // Add the name with a subtle glow effect
        $textX = $this->layerWidth / 2 - (strlen($name) * 3);
        $textY = ($this->layerHeight / 2 - $this->starSize / 2) - 15;
        
        // Add a subtle text shadow/glow
        $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, 80);
        Text::write($image, $name, $textX + 1, $textY + 1, $shadowColor);
        
        // Write the actual text
        Text::write($image, $name, $textX, $textY);
    }

    // TODO: move to dedicated Math class
    private function makeEven($number): int
    {
        $number = intval($number);

        if ($number % 2 === 0) {
            return $number;
        }

        return $number + 1;
    }
}
