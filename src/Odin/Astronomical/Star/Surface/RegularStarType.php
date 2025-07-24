<?php

declare(strict_types=1);

namespace Odin\Astronomical\Star\Surface;

class RegularStarType extends AbstractStarType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Regular';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'core' => '#ffff66',
            'surface' => '#ffffcc',
            'corona' => '#ffffdd',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function applySurfaceEffects($surface, int $size, array $colors): void
    {
        // Create a base gradient for the star from center to edge
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $distanceFromCenter = sqrt(pow($x - $centerX, 2) + pow($y - $centerY, 2));
                
                if ($distanceFromCenter <= $radius) {
                    // Calculate gradient based on distance from center
                    $gradientFactor = $distanceFromCenter / $radius;
                    
                    // Brighter in center, dimmer at edges - with more color variation
                    $r = 255 - (int)(50 * $gradientFactor); // Reduce brightness at edges
                    $g = 240 - (int)(70 * $gradientFactor);
                    $b = (int)(102 + (153 * (1 - $gradientFactor))); // 102 to 255
                    
                    // Add some noise for texture
                    $noise = rand(-15, 15);
                    $r = max(0, min(255, $r + $noise));
                    $g = max(0, min(255, $g + $noise));
                    $b = max(0, min(255, $b + $noise));
                    
                    // More transparent at edges
                    $alpha = (int)(40 * $gradientFactor); // Increased alpha for less brightness
                    
                    $color = imagecolorallocatealpha($surface, $r, $g, $b, $alpha);
                    imagesetpixel($surface, $x, $y, $color);
                }
            }
        }
        
        // Add solar granulation effect (small cells) - with more contrast
        $this->addGranulationEffect($surface, $size, $colors);
        
        // Add some brighter spots (solar flares) - fewer but more distinct
        $this->addSolarFlares($surface, $size, $colors);
        
        // Add some decorative bands for visual interest
        $this->addDecorativeBands($surface, $size, $colors);
    }
    
    /**
     * Add solar granulation effect (the cell-like pattern on a star's surface)
     */
    private function addGranulationEffect($surface, int $size, array $colors): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Create several small granulation cells
        $cellCount = 150; // Reduced from 200 for less busy appearance
        for ($i = 0; $i < $cellCount; $i++) {
            // Random position within the star
            $angle = rand(0, 360) * M_PI / 180;
            $distance = rand(0, (int)($radius * 0.9));
            
            $x = $centerX + $distance * cos($angle);
            $y = $centerY + $distance * sin($angle);
            
            // Cell size varies
            $cellSize = rand(3, 8); // Slightly larger cells
            
            // Cell color - more contrast with surroundings
            $cellColor = imagecolorallocatealpha(
                $surface,
                rand(220, 255),
                rand(180, 220),
                rand(100, 150),
                rand(30, 60)
            );
            
            // Draw cell with irregular shape
            imagefilledellipse($surface, (int)$x, (int)$y, $cellSize, (int)($cellSize * rand(80, 120) / 100), $cellColor);
        }
    }
    
    /**
     * Add solar flares and bright spots
     */
    private function addSolarFlares($surface, int $size, array $colors): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Add 3-6 solar flares (fewer but more distinct)
        $flareCount = rand(3, 6);
        for ($i = 0; $i < $flareCount; $i++) {
            // Position near the edge
            $angle = rand(0, 360) * M_PI / 180;
            $distance = rand((int)($radius * 0.7), (int)($radius * 0.95));
            
            $x = $centerX + $distance * cos($angle);
            $y = $centerY + $distance * sin($angle);
            
            // Flare size - larger for more visual impact
            $flareSize = rand(10, 20);
            
            // Bright yellow-orange color
            $flareColor = imagecolorallocatealpha($surface, 255, rand(200, 255), rand(100, 150), 0);
            
            // Draw the flare
            imagefilledellipse($surface, (int)$x, (int)$y, $flareSize, $flareSize, $flareColor);
            
            // Add glow around the flare
            for ($j = 1; $j <= 3; $j++) {
                $glowSize = $flareSize + $j * 4;
                $glowColor = imagecolorallocatealpha($surface, 255, rand(180, 220), rand(80, 120), 20 + $j * 20);
                imagefilledellipse($surface, (int)$x, (int)$y, $glowSize, $glowSize, $glowColor);
            }
        }
    }
    
    /**
     * Add decorative bands for visual interest
     */
    private function addDecorativeBands($surface, int $size, array $colors): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Add 2-3 subtle bands
        $bandCount = rand(2, 3);
        for ($i = 0; $i < $bandCount; $i++) {
            // Band position - evenly distributed
            $bandRadius = $radius * (0.3 + ($i * 0.25));
            
            // Band color - slightly different from base
            $bandColor = imagecolorallocatealpha(
                $surface,
                rand(220, 255),
                rand(200, 240),
                rand(120, 180),
                rand(50, 70)
            );
            
            // Draw a circle for the band
            imageellipse($surface, $centerX, $centerY, (int)($bandRadius * 2), (int)($bandRadius * 2), $bandColor);
            
            // Add some texture to the band
            $this->addBandTexture($surface, $size, $bandRadius, $bandColor);
        }
    }
    
    /**
     * Add texture to decorative bands
     */
    private function addBandTexture($surface, int $size, float $bandRadius, $bandColor): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        
        // Add texture points along the band
        $pointCount = rand(20, 30);
        for ($i = 0; $i < $pointCount; $i++) {
            $angle = ($i * 360 / $pointCount) * M_PI / 180;
            
            // Add some variation to the radius
            $adjustedRadius = $bandRadius * (0.95 + (rand(0, 10) / 100));
            
            $x = $centerX + $adjustedRadius * cos($angle);
            $y = $centerY + $adjustedRadius * sin($angle);
            
            // Draw a small point
            $pointSize = rand(2, 4);
            imagefilledellipse($surface, (int)$x, (int)$y, $pointSize, $pointSize, $bandColor);
        }
    }
}
