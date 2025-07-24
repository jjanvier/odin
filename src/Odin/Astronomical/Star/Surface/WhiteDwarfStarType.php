<?php

declare(strict_types=1);

namespace Odin\Astronomical\Star\Surface;

class WhiteDwarfStarType extends AbstractStarType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'WhiteDwarf';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'core' => '#ffffff',
            'surface' => '#ccffff',
            'corona' => '#eeffff',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function applySurfaceEffects($surface, int $size, array $colors): void
    {
        // Create a base gradient for the white dwarf
        $this->createBaseGradient($surface, $size);
        
        // Add an intense but not overwhelming bright center
        $this->addIntenseBrightCenter($surface, $size);
        
        // Add blue-tinted regions
        $this->addBlueTintedRegions($surface, $size);
        
        // Add crystalline-like patterns
        $this->addCrystallinePatterns($surface, $size);
        
        // Add decorative rings for visual appeal
        $this->addDecorativeRings($surface, $size);
    }
    
    /**
     * Create a base gradient for the white dwarf
     */
    private function createBaseGradient($surface, int $size): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $distanceFromCenter = sqrt(pow($x - $centerX, 2) + pow($y - $centerY, 2));
                
                if ($distanceFromCenter <= $radius) {
                    // Calculate gradient based on distance from center - less steep gradient
                    $gradientFactor = pow($distanceFromCenter / $radius, 0.8); // Power < 1 makes gradient more uniform
                    
                    // White-blue gradient - more blue tint, less variation from center to edge
                    $r = 200 - (int)(40 * $gradientFactor); // Reduced difference (was 60)
                    $g = 210 - (int)(30 * $gradientFactor); // Reduced difference (was 50)
                    $b = 255 - (int)(10 * $gradientFactor); // Keep blue high
                    
                    // Add some noise for texture
                    $noise = rand(-15, 15);
                    $r = max(160, min(220, $r + $noise));
                    $g = max(180, min(230, $g + $noise));
                    $b = max(220, min(255, $b + $noise));
                    
                    // More transparent at edges and overall
                    $alpha = (int)(30 * $gradientFactor); // Reduced from 40 for less contrast
                    
                    $color = imagecolorallocatealpha($surface, $r, $g, $b, $alpha);
                    imagesetpixel($surface, $x, $y, $color);
                }
            }
        }
    }
    
    /**
     * Add an intense bright center characteristic of white dwarfs
     */
    private function addIntenseBrightCenter($surface, int $size): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        
        // Create multiple layers of decreasing size and opacity for a glowing effect
        $maxLayers = 3; // Reduced from 4
        for ($i = 0; $i < $maxLayers; $i++) {
            $layerSize = (int)(($size / 3) * (($maxLayers - $i) / $maxLayers));
            $opacity = 40 + ($i * 10); // More transparent (was 30 + i*15)
            
            // More blue tint in the center, but less bright
            $centerColor = imagecolorallocatealpha($surface, 210, 220, 250, $opacity);
            imagefilledellipse($surface, (int)$centerX, (int)$centerY, $layerSize, $layerSize, $centerColor);
        }
        
        // Add a small, less bright core
        $coreSize = (int)($size / 12); // Even smaller core (was size/10)
        $coreColor = imagecolorallocatealpha($surface, 220, 230, 255, 30); // More transparency (was 20)
        imagefilledellipse($surface, (int)$centerX, (int)$centerY, $coreSize, $coreSize, $coreColor);
    }
    
    /**
     * Add subtle blue-tinted regions
     */
    private function addBlueTintedRegions($surface, int $size): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Add 25-35 subtle blue regions
        $regionCount = rand(25, 35);
        for ($i = 0; $i < $regionCount; $i++) {
            // Position throughout the star
            $angle = rand(0, 360) * M_PI / 180;
            $distance = rand((int)($radius * 0.2), (int)($radius * 0.95)); // Allow closer to center
            
            $x = $centerX + $distance * cos($angle);
            $y = $centerY + $distance * sin($angle);
            
            $regionSize = rand(3, 8);
            
            // Blue-white color with varying intensity - more blue
            $blueIntensity = rand(200, 255);
            $regionColor = imagecolorallocatealpha(
                $surface,
                rand(160, 200), // Reduced from 180-220
                rand(180, 220), // Reduced from 180-220
                $blueIntensity,
                rand(30, 60)
            );
            
            // Draw the region
            imagefilledellipse($surface, (int)$x, (int)$y, $regionSize, $regionSize, $regionColor);
        }
    }
    
    /**
     * Add crystalline-like patterns to simulate the highly compressed carbon surface
     */
    private function addCrystallinePatterns($surface, int $size): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Create 4-8 crystalline pattern regions (reduced from 5-10)
        $patternCount = rand(4, 8);
        for ($i = 0; $i < $patternCount; $i++) {
            // Position these patterns toward the outer regions
            $angle = rand(0, 360) * M_PI / 180;
            $distance = rand((int)($radius * 0.5), (int)($radius * 0.9));
            
            $centerPatternX = $centerX + $distance * cos($angle);
            $centerPatternY = $centerY + $distance * sin($angle);
            
            // Create 3-6 radiating lines from this center (reduced from 5-8)
            $lineCount = rand(3, 6);
            for ($j = 0; $j < $lineCount; $j++) {
                $lineAngle = rand(0, 360) * M_PI / 180;
                $lineLength = rand(5, 15); // Shorter lines
                
                $endX = $centerPatternX + $lineLength * cos($lineAngle);
                $endY = $centerPatternY + $lineLength * sin($lineAngle);
                
                // Line color - more subdued
                $lineColor = imagecolorallocatealpha(
                    $surface,
                    rand(200, 240), // Reduced from 220-255
                    rand(200, 240), // Reduced from 220-255
                    rand(220, 255),
                    rand(30, 60) // More transparent
                );
                
                // Draw the line
                imageline(
                    $surface,
                    (int)$centerPatternX,
                    (int)$centerPatternY,
                    (int)$endX,
                    (int)$endY,
                    $lineColor
                );
                
                // Sometimes add a small dot at the end of the line
                if (rand(0, 2) == 0) {
                    $dotColor = imagecolorallocatealpha(
                        $surface,
                        rand(200, 240), // Reduced brightness
                        rand(200, 240), // Reduced brightness
                        rand(220, 255),
                        rand(20, 40)
                    );
                    imagefilledellipse($surface, (int)$endX, (int)$endY, 2, 2, $dotColor);
                }
            }
        }
        
        // Add a few bright spots to simulate heat pockets - fewer than before
        for ($i = 0; $i < 10; $i++) { // Reduced from 15
            $angle = rand(0, 360) * M_PI / 180;
            $distance = rand(0, (int)($radius * 0.8));
            
            $x = $centerX + $distance * cos($angle);
            $y = $centerY + $distance * sin($angle);
            
            $spotSize = rand(1, 3);
            $spotColor = imagecolorallocatealpha($surface, 240, 240, 255, 20); // Added transparency
            
            imagefilledellipse($surface, (int)$x, (int)$y, $spotSize, $spotSize, $spotColor);
        }
    }
    
    /**
     * Add decorative rings for visual appeal
     */
    private function addDecorativeRings($surface, int $size): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        
        // Add 2-3 subtle rings
        $ringCount = rand(2, 3);
        for ($i = 0; $i < $ringCount; $i++) {
            // Ring radius - evenly distributed
            $ringRadius = ($size / 2) * (0.4 + ($i * 0.2));
            
            // Ring color - blue-white with transparency
            $ringColor = imagecolorallocatealpha(
                $surface,
                rand(200, 230),
                rand(200, 230),
                rand(230, 255),
                rand(50, 70)
            );
            
            // Draw the ring
            imageellipse($surface, $centerX, $centerY, (int)($ringRadius * 2), (int)($ringRadius * 2), $ringColor);
            
            // Add some texture to the ring
            $this->addRingTexture($surface, $size, $ringRadius, $ringColor);
        }
    }
    
    /**
     * Add texture to decorative rings
     */
    private function addRingTexture($surface, int $size, float $ringRadius, $ringColor): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        
        // Add texture points along the ring
        $pointCount = rand(15, 25);
        for ($i = 0; $i < $pointCount; $i++) {
            $angle = ($i * 360 / $pointCount) * M_PI / 180;
            
            // Add some variation to the radius
            $adjustedRadius = $ringRadius * (0.95 + (rand(0, 10) / 100));
            
            $x = $centerX + $adjustedRadius * cos($angle);
            $y = $centerY + $adjustedRadius * sin($angle);
            
            // Draw a small point
            $pointSize = rand(1, 3);
            imagefilledellipse($surface, (int)$x, (int)$y, $pointSize, $pointSize, $ringColor);
        }
    }
}
