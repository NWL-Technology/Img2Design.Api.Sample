using System.Text.Json.Serialization;

namespace Img2Design.Api.RemoveBackground.Sample.Model
{
    public sealed class ApiConfigRequest
    {
        public RenderMode Mode { get; set; } = RenderMode.RemoveBackground;

        public ElementPositionSimple Position { get; set; } = ElementPositionSimple.Auto;

        // Look & feel
        public double Zoom { get; set; }          // 0.5..1
        public double Round { get; set; } = 0;           // 0..1 (1 => circle when square)

        // Output
        public int? Width { get; set; }                  // if only one is set: keep aspect ratio (Plain) or square (Center/BottomCenter)
        public int? Height { get; set; }
        public OutputFormat Format { get; set; } = OutputFormat.Webp; 

        // Background 
        public BackgroundOptions Background { get; set; } = new BackgroundOptions();
    }

    public enum OutputFormat
    {
        Webp,
        Jpeg,
        Png
    }

    public enum RenderMode
    {
        Plain,            // keep aspect ratio; no background manipulation / optional blurred with  UseSameImage
        RemoveBackground  // subject cut-out; optional blurred / colored / image background
    }

    public enum ElementPositionSimple
    {
        Auto,         // default: Plain => None; RemoveBackground => Center
        None,         // keep aspect ratio
        Center,       // square crop centered
        BottomCenter  // square crop anchored at bottom center (ignored in Plain)
    }

    public sealed class BackgroundOptions
    {
        public bool? UseSameImage { get; set; }          // reuse main image as bg (for blur)
        public string? Color { get; set; }               // hex color (e.g. "#00000000" or "#FFFFFF")
        public string? ImageUrl { get; set; }            // optional external bg
        public string? ImageBase64 { get; set; }         // optional base64 bg
        public int? Blur { get; set; }                   // 0..4
    }    
}
