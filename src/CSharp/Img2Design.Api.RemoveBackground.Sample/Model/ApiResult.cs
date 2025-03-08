namespace Img2Design.Api.RemoveBackground.Sample.Model
{
    public class ApiResult
    {
        public string? RequestId { get; set; }
        public byte[]? ImageContent { get; set; }

        public ApiResult(string? requestId, byte[]? imageContent)
        {
            RequestId = requestId;
            ImageContent = imageContent;
        }
    }
}
