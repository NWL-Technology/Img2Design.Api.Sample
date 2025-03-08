namespace Img2Design.Api.RemoveBackground.Sample.Model
{
    public class ApiRequest
    {
        public string? RequestId { get; set; } //optionnal, used for parallel requests to identify the result of a specific request
        public string SourceImagePath { get; set; }
        public ApiConfigRequest? Config { get; set; } //optionnal, used for customizations
    }
}
