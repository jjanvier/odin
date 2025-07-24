<?php

declare(strict_types=1);

namespace Odin\Astronomical\Star\Surface;

/**
 * A StarType defines the characteristics of a star.
 * Its color, size, temperature, and appearance.
 */
interface StarTypeInterface
{
    /**
     * Generate the representation of this star type and returns it as a resource image.
     */
    public function generate(int $size);

    /**
     * Return the name of this Star Type.
     */
    public function getName(): string;

    /**
     * Return the color palette for this Star Type.
     * A palette *MUST* contain the "core" key, as it is used for the glowy color of the star.
     */
    public function getColorPalette(): array;
}
