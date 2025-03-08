using Img2Design.Api.RemoveBackground.Sample.Model;

class Program
{
    private static string ouputDirectory = "outputs";
    private static string outputFilePath(string? requestId) => $"{ouputDirectory}/background_removed{requestId ?? ""}.png";

    static async Task Main(string[] args)
    {

        Directory.CreateDirectory(ouputDirectory);
        var imagePath = "sample/img_sample.jpg";

        ////simple background removal without background
        var apiRequest = new ApiRequest
        {
            RequestId = Guid.NewGuid().ToString(),
            SourceImagePath = imagePath,        //    
        };
        await Background.Remove(apiRequest).ContinueWith(apiResponse => SaveFile(apiResponse.Result));


        //////simple background removal with a image background blur
        //apiRequest = new ApiRequest
        //{
        //    RequestId = Guid.NewGuid().ToString(),
        //    SourceImagePath = imagePath,
        //    Config = new ApiConfigRequest
        //    {
        //        BgImageUrl = "https://github.com/makccr/wallpapers/blob/master/wallpapers/abstract/lucas-benjamin-R79qkPYvrcM-unsplash.jpg?raw=true",
        //        BgImageBlurLevel = 2,
        //        ElementLocation = "center"
        //    }
        //};
        //await Background.Remove(apiRequest).ContinueWith(apiResponse => SaveFile(apiResponse.Result));

        ////simple background removal with a background color
        //apiRequest = new ApiRequest
        //{
        //    RequestId = Guid.NewGuid().ToString(),
        //    SourceImagePath = imagePath,
        //    Config = new ApiConfigRequest
        //    {
        //        BgColor = "#689bbd",
        //        ElementLocation = "bottomCenter"
        //    }
        //};
        //await Background.Remove(apiRequest).ContinueWith(apiResponse => SaveFile(apiResponse.Result));

        //multiple background removal
        var apiRequests = Enumerable.Range(0, 5).Select(x => (new ApiRequest
        {
            RequestId = x.ToString(),
            SourceImagePath = imagePath,
            Config = new ApiConfigRequest
            {
                BgColor = GetRandomBackgroundColor(),
                ElementLocation = "center"
            }
        })).ToList();

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
