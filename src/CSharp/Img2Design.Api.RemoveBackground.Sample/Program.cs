using Img2Design.Api.RemoveBackground.Sample.Model;

class Program
{
    private static string ouputDirectory = "outputs";
    private static string outputFilePath(string? requestId) => $"{ouputDirectory}/background_removed{requestId ?? ""}.png";

    static async Task Main(string[] args)
    {

        Directory.CreateDirectory(ouputDirectory);
        var imagePath = "sample/img_sample.jpg";      
        var imageCarPath = "sample/img_sample_car.jpg";
        var imageFoodPath = "sample/img_sample_food.jpg";
        var imagePersonPath = "sample/img_sample_person.jpg";

        ////simple background removal 
        await Background.Remove(RequestSample.ReqRemoveBgOnly(imageFoodPath))
            .ContinueWith(apiResponse => SaveFile(apiResponse.Result));

        ////simple background removal with risze
        await Background.Remove(RequestSample.ReqRemovebgWidth400(imageFoodPath))
            .ContinueWith(apiResponse => SaveFile(apiResponse.Result));

        //multiple background removal
        var apiRequests = new List<ApiRequest>() {
                RequestSample.ReqPlainCenter80ZoomJpeg(imagePath),
                RequestSample.ReqPlainRounded300Width(imagePersonPath),
                RequestSample.ReqRemovebgAuto90Zoom30RoundWebpColorBg(imageCarPath),
                RequestSample.ReqRemovebgNone90zoom15Round500SquareWebpBlurredBg(imageFoodPath),
            };

        await Background.RemoveMany(apiRequests).ContinueWith(apiResponses =>
        {
            foreach (var result in apiResponses.Result)
            {
                SaveFile(result);
            }
        });
    }

    private static void SaveFile(ApiResult apiResult)
    {
        var filePath = outputFilePath(apiResult.RequestId);

        if (apiResult.ImageContent == null)
        {
            Console.WriteLine($"Image processing failed for request [{apiResult.RequestId}]");
            return;
        }
        File.WriteAllBytes(filePath, apiResult.ImageContent);
        Console.WriteLine($"Background removed and saved as {filePath}");
    }

    public static string GetRandomBackgroundColor()
    {
        Random random = new Random();
        int r = random.Next(256); // Red (0-255)
        int g = random.Next(256); // Green (0-255)
        int b = random.Next(256); // Blue (0-255)

        return $"#{r:X2}{g:X2}{b:X2}"; // Format as #RRGGBB
    }
}
