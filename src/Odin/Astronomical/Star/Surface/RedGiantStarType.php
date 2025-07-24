<?php

declare(strict_types=1);

namespace Odin\Astronomical\Star\Surface;

class RedGiantStarType extends AbstractStarType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'RedGiant';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'core' => '#ff2200',
            'surface' => '#ff5500',
            'corona' => '#ff8844',
        ];
    }

    /**
     * Apply surface effects to the star
     */
    protected function applySurfaceEffects($surface, int $size, array $colors): void
    {
        // Create a base gradient for the red giant
        $this->createBaseGradient($surface, $size);
        
        // Add horizontal bands similar to gas planets
        $this->addHorizontalBands($surface, $size);
        
        // Add large convection cells
        $this->addConvectionCells($surface, $size);
        
        // Add sunspots
        $this->addSunspots($surface, $size);
        
        // Add prominences (solar flares)
        $this->addProminences($surface, $size);
    }
    
    /**
     * Create a base gradient for the red giant
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
                    // Calculate gradient based on distance from center
                    $gradientFactor = $distanceFromCenter / $radius;
                    
                    // Red giant has orange-red gradient - less intense than before
                    $r = 220 - (int)(70 * $gradientFactor); // Reduced from 255
                    $g = 140 - (int)(120 * $gradientFactor); // Reduced from 150
                    $b = 20; // Constant low blue
                    
                    // Apply some noise for texture
                    $noise = rand(-20, 20);
                    $r = max(150, min(220, $r + $noise));
                    $g = max(20, min(140, $g + $noise));
                    
                    // More transparent at edges and overall
                    $alpha = (int)(40 * $gradientFactor); // Increased alpha for less brightness
                    
                    $color = imagecolorallocatealpha($surface, $r, $g, $b, $alpha);
                    imagesetpixel($surface, $x, $y, $color);
                }
            }
        }
    }
    
    /**
     * Add horizontal bands similar to gas planets
     */
    private function addHorizontalBands($surface, int $size): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Create 16-24 horizontal bands (doubled from 8-12)
        $bandCount = rand(16, 24);
        $bandHeight = (int)($size / $bandCount);
        
        for ($i = 0; $i < $bandCount; $i++) {
            // Band position - ensure it's an integer
            $bandY = (int)(($i * $bandHeight) + rand(-2, 2)); // Cast to int
            
            // Band color - alternating darker and lighter with subtle differences
            $isDark = ($i % 2 == 0);
            
            if ($isDark) {
                $bandColor = imagecolorallocatealpha(
                    $surface,
                    rand(170, 200),
                    rand(60, 90),
                    rand(0, 20),
                    rand(50, 70) // More transparent
                );
            } else {
                $bandColor = imagecolorallocatealpha(
                    $surface,
                    rand(200, 230),
                    rand(90, 130),
                    rand(10, 30),
                    rand(40, 60) // More transparent
                );
            }
            
            // Draw the band with wavy edges
            $this->drawWavyBand($surface, $size, $bandY, $bandHeight, $bandColor);
            
            // Add some texture to the band - but only to some bands to avoid overcrowding
            if (rand(0, 3) > 0) { // 75% chance to add texture
                $this->addBandTexture($surface, $size, $bandY, $bandHeight, $bandColor, $isDark);
            }
        }
    }
    
    /**
     * Draw a wavy horizontal band
     */
    private function drawWavyBand($surface, int $size, int $bandY, int $bandHeight, $bandColor): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Create wavy top and bottom edges - smaller amplitude
        $waveAmplitude = rand(2, 5); // Reduced from 3-8
        $waveFrequency = rand(3, 5); // Increased from 2-4
        
        // Draw the band
        for ($x = 0; $x < $size; $x++) {
            // Calculate distance from center horizontally
            $distX = $x - $centerX;
            $distFromCenter = abs($distX);
            
            if ($distFromCenter < $radius) {
                // Calculate vertical position adjustment based on wave
                $waveOffset = $waveAmplitude * sin($waveFrequency * $distX * M_PI / $radius);
                
                // Calculate band top and bottom with wave - ensure they're integers
                $bandTop = (int)($bandY + $waveOffset);
                $bandBottom = (int)($bandY + $bandHeight + $waveOffset);
                
                // Draw vertical line for this x position
                for ($y = max(0, $bandTop); $y < min($size, $bandBottom); $y++) {
                    // Check if point is within the circle
                    $distY = $y - $centerY;
                    $distTotal = sqrt($distX * $distX + $distY * $distY);
                    
                    if ($distTotal <= $radius) {
                        // Apply some transparency variation for texture
                        $alpha = imagecolorsforindex($surface, imagecolorat($surface, (int)$x, (int)$y))['alpha'];
                        $newColor = imagecolorallocatealpha(
                            $surface,
                            imagecolorsforindex($surface, $bandColor)['red'],
                            imagecolorsforindex($surface, $bandColor)['green'],
                            imagecolorsforindex($surface, $bandColor)['blue'],
                            max($alpha - 5, 0) // Less opacity change (was -10)
                        );
                        imagesetpixel($surface, (int)$x, (int)$y, $newColor);
                    }
                }
            }
        }
    }
    
    /**
     * Add texture to a horizontal band
     */
    private function addBandTexture($surface, int $size, int $bandY, int $bandHeight, $bandColor, bool $isDark): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Add more swirls and spots to the band
        $featureCount = rand(10, 20); // Increased from 5-15
        for ($i = 0; $i < $featureCount; $i++) {
            // Position within the band
            $x = rand(0, $size);
            $y = rand($bandY, $bandY + $bandHeight);
            
            // Check if within the circle
            $distX = $x - $centerX;
            $distY = $y - $centerY;
            $distTotal = sqrt($distX * $distX + $distY * $distY);
            
            if ($distTotal <= $radius) {
                // Feature size - smaller
                $featureSize = rand(3, 8); // Reduced from 5-15
                
                // Feature color - contrast with band but more subtle
                if ($isDark) {
                    $featureColor = imagecolorallocatealpha(
                        $surface,
                        rand(190, 220),
                        rand(80, 120),
                        rand(10, 30),
                        rand(40, 60)
                    );
                } else {
                    $featureColor = imagecolorallocatealpha(
                        $surface,
                        rand(160, 190),
                        rand(50, 90),
                        rand(0, 20),
                        rand(50, 70)
                    );
                }
                
                // Draw the feature - oval shape stretched horizontally
                imagefilledellipse(
                    $surface, 
                    (int)$x, 
                    (int)$y, 
                    (int)($featureSize * 2), // Wider horizontally
                    (int)$featureSize, 
                    $featureColor
                );
            }
        }
        
        // Add some small streaks along the band
        $streakCount = rand(5, 10);
        for ($i = 0; $i < $streakCount; $i++) {
            // Position within the band
            $startX = rand(0, $size);
            $startY = rand($bandY + 1, min($size - 1, $bandY + $bandHeight - 1)); // Ensure within bounds
            
            // Check if within the circle
            $distX = $startX - $centerX;
            $distY = $startY - $centerY;
            $distTotal = sqrt($distX * $distX + $distY * $distY);
            
            if ($distTotal <= $radius * 0.9) {
                // Streak length
                $length = rand(5, 15);
                $endX = min($size - 1, $startX + $length); // Ensure within bounds
                
                // Get base colors from band color
                $baseColors = imagecolorsforindex($surface, $bandColor);
                
                // Apply bounded adjustments to ensure values stay within 0-255
                $r = max(0, min(255, $baseColors['red'] + rand(-20, 20)));
                $g = max(0, min(255, $baseColors['green'] + rand(-20, 20)));
                $b = max(0, min(255, $baseColors['blue'] + rand(-10, 10)));
                $a = rand(30, 50);
                
                // Streak color with bounded values
                $streakColor = imagecolorallocatealpha(
                    $surface,
                    $r,
                    $g,
                    $b,
                    $a
                );
                
                // Draw streak
                imageline($surface, (int)$startX, (int)$startY, (int)$endX, (int)$startY, $streakColor);
            }
        }
    }
    
    /**
     * Add large convection cells
     */
    private function addConvectionCells($surface, int $size): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Create fewer large convection cells
        $cellCount = rand(15, 25); // Reduced from 30-50
        for ($i = 0; $i < $cellCount; $i++) {
            // Random position within the star
            $angle = rand(0, 360) * M_PI / 180;
            $distance = rand(0, (int)($radius * 0.9));
            
            $x = $centerX + $distance * cos($angle);
            $y = $centerY + $distance * sin($angle);
            
            // Cell size varies - larger than before
            $cellSize = rand(10, 25); // Increased from 8-20
            
            // Cell color - more subdued
            $cellColor = imagecolorallocatealpha(
                $surface,
                rand(180, 220), // Reduced from 200-255
                rand(60, 120), // Reduced from 80-150
                rand(0, 20),
                rand(30, 60) // More transparent
            );
            
            // Draw cell with irregular shape
            imagefilledellipse($surface, (int)$x, (int)$y, $cellSize, (int)($cellSize * rand(80, 120) / 100), $cellColor);
        }
    }
    
    /**
     * Add sunspots
     */
    private function addSunspots($surface, int $size): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Create fewer sunspots
        $spotCount = rand(5, 10); // Reduced from 10-20
        for ($i = 0; $i < $spotCount; $i++) {
            // Random position within the star
            $angle = rand(0, 360) * M_PI / 180;
            $distance = rand(0, (int)($radius * 0.8));
            
            $x = $centerX + $distance * cos($angle);
            $y = $centerY + $distance * sin($angle);
            
            // Spot size varies
            $spotSize = rand(5, 15); // Slightly larger
            
            // Dark spot color
            $spotColor = imagecolorallocatealpha($surface, rand(80, 120), rand(20, 60), 0, rand(0, 20));
            
            // Draw the spot
            imagefilledellipse($surface, (int)$x, (int)$y, $spotSize, $spotSize, $spotColor);
            
            // Add darker center to some spots
            if (rand(0, 2) == 0) {
                $centerColor = imagecolorallocatealpha($surface, rand(60, 100), rand(0, 20), 0, rand(20, 40));
                imagefilledellipse($surface, (int)$x, (int)$y, (int)($spotSize / 2), (int)($spotSize / 2), $centerColor);
            }
        }
    }
    
    /**
     * Add prominences (solar flares)
     */
    private function addProminences($surface, int $size): void
    {
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        
        // Create fewer prominences
        $flareCount = rand(3, 6); // Reduced from 5-10
        for ($i = 0; $i < $flareCount; $i++) {
            // Position near the edge
            $angle = rand(0, 360) * M_PI / 180;
            $distance = rand((int)($radius * 0.8), (int)($radius * 0.95));
            
            $x = $centerX + $distance * cos($angle);
            $y = $centerY + $distance * sin($angle);
            
            // Flare size - larger for more visual impact
            $flareSize = rand(10, 20); // Increased from 5-15
            
            // Bright orange-red color
            $flareColor = imagecolorallocatealpha(
                $surface,
                rand(220, 255),
                rand(100, 180),
                rand(0, 50),
                rand(0, 20)
            );
            
            // Draw the prominence
            imagefilledellipse($surface, (int)$x, (int)$y, $flareSize, $flareSize, $flareColor);
            
            // Add glow around the prominence
            for ($j = 1; $j <= 3; $j++) { // Reduced from 4 layers
                $glowSize = $flareSize + $j * 3;
                $glowColor = imagecolorallocatealpha($surface, 255, rand(100, 180), rand(30, 80), 30 + $j * 15); // More transparent
                imagefilledellipse($surface, (int)$x, (int)$y, $glowSize, $glowSize, $glowColor);
            }
            
            // Sometimes add a prominence extension (like a solar flare)
            if (rand(0, 2) == 0) {
                $extAngle = $angle + (rand(-30, 30) * M_PI / 180);
                $extLength = rand($flareSize, $flareSize * 2);
                
                $extEndX = $x + cos($extAngle) * $extLength;
                $extEndY = $y + sin($extAngle) * $extLength;
                
                // Draw a thick line for the extension
                $this->drawThickLine($surface, (int)$x, (int)$y, (int)$extEndX, (int)$extEndY, $flareColor, rand(3, 6));
            }
        }
    }
    
    /**
     * Draw a thick line
     */
    private function drawThickLine($image, $x1, $y1, $x2, $y2, $color, $thickness): void
    {
        $t = $thickness / 2 - 0.5;
        
        if ($x1 == $x2 || $y1 == $y2) {
            imagesetthickness($image, $thickness);
            imageline($image, (int)$x1, (int)$y1, (int)$x2, (int)$y2, $color);
            imagesetthickness($image, 1);
            return;
        }
        
        $k = ($y2 - $y1) / ($x2 - $x1);
        $a = $t / sqrt(1 + pow($k, 2));
        $points = [
            (int)round($x1 - (1+$k)*$a), (int)round($y1 + (1-$k)*$a),
            (int)round($x1 - (1-$k)*$a), (int)round($y1 - (1+$k)*$a),
            (int)round($x2 + (1+$k)*$a), (int)round($y2 - (1-$k)*$a),
            (int)round($x2 + (1-$k)*$a), (int)round($y2 + (1+$k)*$a),
        ];
        
        imagefilledpolygon($image, $points, 4, $color);
    }
}
