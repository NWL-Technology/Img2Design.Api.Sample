namespace Img2Design.Api.RemoveBackground.Sample.Model
{
    public class ApiConfigRequest
    {
        public string ElementLocation { get; set; } // center, bottomCenter, aspectRatio
        public double Zoom { get; set; } = 0.8;
        public string BgImageBase64 { get; set; }
        public int BgImageBlurLevel { get; set; } // possible value 1, 2, 3
        public string BgImageUrl { get; set; }
        public string BgColor { get; set; }
    }
}
