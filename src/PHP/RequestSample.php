<?php

class RequestSample
{
    public static function reqRemoveBgOnly(string $imagePath): ApiRequest {
        return new ApiRequest(
            "remove-bg-only",
            $imagePath
        );
    }

    public static function reqPlainRounded300Width(string $imagePath): ApiRequest {
        return new ApiRequest(
            "plain-rounded-300-width-webp",
            $imagePath,
            new ApiConfigRequest(
                mode: RenderMode::Plain,
                position: ElementPositionSimple::Center,
                zoom: 95,
                round: 1.0,
                width: 300,
                format: OutputFormat::Webp
            )
        );
    }

    public static function reqRemovebgNone90zoom15Round500SquareWebpBlurredBg(string $imagePath): ApiRequest {
        $background = new BackgroundOptions();
        $background->useSameImage = true;
        $background->blur = 3;

        return new ApiRequest(
            "removebg-none-90zoom-15round-500square-webp-blurred-bg",
            $imagePath,
            new ApiConfigRequest(
                mode: RenderMode::RemoveBackground,
                position: ElementPositionSimple::None,
                zoom: 90,
                round: 15.0,
                width: 500,
                height: 500,
                format: OutputFormat::Webp,
                background: $background
            )
        );
    }

    public static function reqRemovebgWidth400(string $imagePath): ApiRequest {
        return new ApiRequest(
            "removebg-400-width-auto",
            $imagePath,
            new ApiConfigRequest(
                mode: RenderMode::RemoveBackground,
                position: ElementPositionSimple::Auto,
                width: 400
            )
        );
    }

    public static function reqRemovebgAuto90Zoom30RoundWebpColorBg(string $imagePath): ApiRequest {
        $background = new BackgroundOptions();
        $background->useSameImage = false;
        $background->color = "#F6F8FA";
        $background->blur = 0;

        return new ApiRequest(
            "removebg-auto-90zoom-30round-webp-neutral-bg",
            $imagePath,
            new ApiConfigRequest(
                mode: RenderMode::RemoveBackground,
                position: ElementPositionSimple::Auto,
                zoom: 90,
                round: 30.0,
                width: 350,
                height: null,
                format: OutputFormat::Webp,
                background: $background
            )
        );
    }

    public static function reqPlainCenter80ZoomJpeg(string $imagePath): ApiRequest {
        return new ApiRequest(
            "plain-center-75zoom-jpeg",
            $imagePath,
            new ApiConfigRequest(
                mode: RenderMode::Plain,
                position: ElementPositionSimple::Center,
                zoom: 75,
                round: 0.0,
                width: 400,
                height: null,
                format: OutputFormat::Jpeg
            )
        );
    }
}
