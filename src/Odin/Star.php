<?php

declare(strict_types=1);

namespace Odin;

use Odin\Astronomical\Star\Star as InternalStar;
use Odin\Drawer\Gd\LayerOrchestrator;

/**
 * @author @jjanvier
 */
class Star
{
    /** @var string */
    private $type;

    /** @var int */
    private $diameter;

    /** @var LayerOrchestrator */
    private $layerOrchestrator;

    /** @var Configuration */
    private $configuration;

    public function __construct(?Configuration $configuration = null)
    {
        $this->layerOrchestrator = new LayerOrchestrator();
        $this->configuration = $configuration ?? new Configuration();
    }

    public function diameter(int $diameterInPixels): self
    {
        $this->diameter = $diameterInPixels;

        return $this;
    }

    public function regular(): self
    {
        $this->type = 'Regular';

        return $this;
    }

    public function redGiant(): self
    {
        $this->type = 'RedGiant';

        return $this;
    }

    public function whiteDwarf(): self
    {
        $this->type = 'WhiteDwarf';

        return $this;
    }

    public function render(): \SplFileObject
    {
        mt_srand($this->configuration->seed());

        if (null === $this->diameter) {
            throw new \LogicException('The star cannot be rendered without a diameter.');
        }

        if (null === $this->type) {
            throw new \LogicException('The star cannot be rendered without a type.');
        }

        $star = new InternalStar($this->type, $this->diameter);
        $this->layerOrchestrator->initTransparentBaseLayer($this->diameter, $this->diameter);
        $this->layerOrchestrator->addLayer($star->render(), -$this->diameter / 2, -$this->diameter / 2);

        $image = $this->layerOrchestrator->render();
        $imagePath = $this->generateImagePath($this->configuration);

        imagepng($image, $imagePath);
        imagedestroy($image);

        return new \SplFileObject($imagePath);
    }

    private function generateImagePath(?Configuration $configuration): string
    {
        $name = uniqid('odin-star-') . '.png';
        $directory = sys_get_temp_dir();
        if (null !== $configuration) {
            $directory = $configuration->directory();
        }

        return $directory . DIRECTORY_SEPARATOR . $name;
    }
}
