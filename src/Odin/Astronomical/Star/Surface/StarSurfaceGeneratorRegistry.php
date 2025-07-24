<?php

declare(strict_types=1);

namespace Odin\Astronomical\Star\Surface;

class StarSurfaceGeneratorRegistry
{
    private $generatorClasses = [
        '\Odin\Astronomical\Star\Surface\RegularStarType',
        '\Odin\Astronomical\Star\Surface\RedGiantStarType',
        '\Odin\Astronomical\Star\Surface\WhiteDwarfStarType',
    ];

    public function forType(string $typeName)
    {
        foreach ($this->generatorClasses as $generatorClass) {
            $generator = new $generatorClass;

            if ($typeName === $generator->getName()) {
                return $generator;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('No generator found for star type "%s"', $typeName)
        );
    }
}
