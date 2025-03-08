using Img2Design.Api.RemoveBackground.Sample.Model;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

internal static class RequestSample
{
    public static ApiRequest ReqRemoveBgOnly(string imagePath) => new ApiRequest
    {
        RequestId = "remove-bg-only",
        SourceImagePath = imagePath,
    };

    public static ApiRequest ReqPlainRounded300Width(string imagePath) => new ApiRequest
    {
        RequestId = "plain-rounded-300-width-webp",
        SourceImagePath = imagePath,
        Config = new ApiConfigRequest
        {
            Mode = RenderMode.Plain,
            Position = ElementPositionSimple.Center,
            Zoom = 95,
            Round = 1,            
            Width = 300,
            Format = OutputFormat.Webp
        }
    };

    public static ApiRequest ReqRemovebgNone90zoom15Round500SquareWebpBlurredBg(string imagePath) => new ApiRequest
    {
        RequestId = "removebg-none-90zoom-15round-500square-webp-blurred-bg",
        SourceImagePath = imagePath,
        Config = new ApiConfigRequest
        {
            Mode = RenderMode.RemoveBackground,
            Position = ElementPositionSimple.None,
            Zoom = 90,
            Round = 15,
            Width = 500,
            Height = 500,
            Format = OutputFormat.Webp,
            Background = new BackgroundOptions
            {
                UseSameImage = true,
                Blur = 3
            }
        }
    };

    public static ApiRequest ReqRemovebgWidth400(string imagePath) => new ApiRequest
    {
        RequestId = "removebg-400-width-auto",
        SourceImagePath = imagePath,
        Config = new ApiConfigRequest
        {
            Mode = RenderMode.RemoveBackground,
            Position = ElementPositionSimple.Auto,
            Width = 400,            
        }
    };

    public static ApiRequest ReqRemovebgAuto90Zoom30RoundWebpColorBg(string imagePath) => new ApiRequest
    {
        RequestId = "removebg-auto-90zoom-30round-webp-neutral-bg",
        SourceImagePath = imagePath,
        Config = new ApiConfigRequest
        {
            Mode = RenderMode.RemoveBackground,
            Position = ElementPositionSimple.Auto,
            Zoom = 0.9,
            Round = 30,
            Width = 350,
            Height = null,
            Format = OutputFormat.Webp,
            Background = new BackgroundOptions
            {
                UseSameImage = false,
                Color = "#F6F8FA",
                Blur = 0
            }
        }
    };

    public static ApiRequest ReqPlainCenter80ZoomJpeg(string imagePath) => new ApiRequest
    {
        RequestId = "plain-center-75zoom-jpeg",
        SourceImagePath = imagePath,
        Config = new ApiConfigRequest
        {
            Mode = RenderMode.Plain,
            Position = ElementPositionSimple.Center,
            Zoom = 75,
            Round = 0,
            Width = 400,
            Height = null,
            Format = OutputFormat.Jpeg,

        }
    };
}
