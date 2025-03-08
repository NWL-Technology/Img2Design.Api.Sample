<?php
class ApiConfigRequest {
    public string $elementLocation; // "center", "bottomCenter", "aspectRatio"
    public float $zoom = 0.8;
    public ?string $bgImageBase64 = null;
    public int $bgImageBlurLevel = 0; // possible values: 1, 2, 3
    public ?string $bgImageUrl = null;
    public ?string $bgColor = null;
}
