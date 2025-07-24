<?php

/**
 * Odin - Celestial Object Generator Examples
 * 
 * This file demonstrates all the celestial objects that can be generated
 * with the Odin library, including planets, stars, and moons with their
 * various configurations.
 * 
 * Run with: php examples.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Odin\Configuration;
use Odin\Planet;
use Odin\Star;
use Odin\Moon;

// Create output directory if it doesn't exist
$outputDir = __DIR__ . '/rendered';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Create a configuration with a fixed seed for reproducible results
$config = new Configuration($outputDir, 12345);

echo "Odin Celestial Object Generator Examples\n";
echo "=======================================\n\n";

// PLANETS
echo "Generating planets...\n";

$planetTypes = [
    'lava' => 'Lava Planet',
    'toxic' => 'Toxic Planet',
    'coldGaz' => 'Cold Gas Planet',
    'hotGaz' => 'Hot Gas Planet',
    'hydroGaz' => 'Hydro Gas Planet',
    'atoll' => 'Atoll Planet',
    'violet' => 'Violet Planet',
    'ashes' => 'Ashes Planet',
    'forest' => 'Forest Planet',
];

foreach ($planetTypes as $method => $description) {
    echo "  - Creating $description...\n";
    $planet = new Planet($config);
    $planet->diameter(300)->$method();
    $file = $planet->render();
    echo "    Generated: " . $file->getPathname() . "\n";
}

// STARS
echo "\nGenerating stars...\n";

$starTypes = [
    'regular' => 'Regular Star',
    'redGiant' => 'Red Giant Star',
    'whiteDwarf' => 'White Dwarf Star',
];

foreach ($starTypes as $method => $description) {
    echo "  - Creating $description...\n";
    $star = new Star($config);
    $star->diameter(300)->$method();
    $file = $star->render();
    echo "    Generated: " . $file->getPathname() . "\n";
}

// MOON
echo "\nGenerating moon...\n";
$moon = new Moon($config);
$moon->diameter(300);
$file = $moon->render();
echo "  Generated: " . $file->getPathname() . "\n";

// SYSTEM EXAMPLE
echo "\nGenerating a complete solar system...\n";

// Create a star
$systemStar = new Star($config);
$systemStar->diameter(500)->regular();
$starFile = $systemStar->render();
echo "  Generated star: " . $starFile->getPathname() . "\n";

// Create some planets with different sizes
$planetSizes = [200, 150, 300, 250, 180];
$planetMethods = array_keys($planetTypes);
for ($i = 0; $i < 5; $i++) {
    $systemPlanet = new Planet($config);
    $method = $planetMethods[$i % count($planetMethods)];
    $systemPlanet->diameter($planetSizes[$i])->$method();
    $planetFile = $systemPlanet->render();
    echo "  Generated planet: " . $planetFile->getPathname() . "\n";
    
    // Add a moon to some planets
    if ($i % 2 == 0) {
        $planetMoon = new Moon($config);
        $planetMoon->diameter($planetSizes[$i] / 3);
        $moonFile = $planetMoon->render();
        echo "    With moon: " . $moonFile->getPathname() . "\n";
    }
}

echo "\nAll examples generated successfully!\n";
echo "Check the 'rendered' directory for the output images.\n";
