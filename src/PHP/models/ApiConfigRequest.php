<?php

enum OutputFormat: string {
    case Webp = 'Webp';
    case Jpeg = 'Jpeg';
    case Png = 'Png';
}

enum RenderMode: string {
    case Plain = 'Plain';
    case RemoveBackground = 'RemoveBackground';
}

enum ElementPositionSimple: string {
    case Auto = 'Auto';
    case None = 'None';
    case Center = 'Center';
    case BottomCenter = 'BottomCenter';
}

class BackgroundOptions {
    public ?bool $useSameImage = null;   // reuse main image as bg (for blur)
    public ?string $color = null;        // hex color (e.g., "#00000000" or "#FFFFFF")
    public ?string $imageUrl = null;     // optional external bg
    public ?string $imageBase64 = null;  // optional base64 bg
    public ?int $blur = null;            // 0..4
}

class ApiConfigRequest {
    public RenderMode $mode;
    public ElementPositionSimple $position;

    // Look & feel
    public float $zoom;   // 0.5..1
    public float $round;  // 0..1 (1 => circle when square)

    // Output
    public ?int $width;
    public ?int $height;
    public OutputFormat $format;

    // Background
    public BackgroundOptions $background;

    public function __construct(
        RenderMode $mode = RenderMode::RemoveBackground,
        ElementPositionSimple $position = ElementPositionSimple::Auto,
        float $zoom = 1.0,
        float $round = 0.0,
        ?int $width = null,
        ?int $height = null,
        OutputFormat $format = OutputFormat::Webp,
        ?BackgroundOptions $background = null
    ) {
        $this->mode = $mode;
        $this->position = $position;
        $this->zoom = $zoom;
        $this->round = $round;
        $this->width = $width;
        $this->height = $height;
        $this->format = $format;
        $this->background = $background ?? new BackgroundOptions();
    }
}