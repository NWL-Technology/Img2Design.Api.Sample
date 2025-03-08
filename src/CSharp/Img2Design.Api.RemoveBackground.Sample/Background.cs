using System.Net.Http.Headers;
using System.Diagnostics;
using Img2Design.Api.RemoveBackground.Sample.Model;
using Newtonsoft.Json;
using System.Text;

public static class Background
{
    private static readonly string apiUrl = "https://api.img2design.io/api/theme/convert";
    private static readonly string userToken = "your_user_token_here"; // Replace with your actual token
    private static readonly int maxParallelRequests = 7; // User defined max number of parallel requests

    public static async Task<ApiResult> Remove(ApiRequest request)
    {
        var stopwatch = new Stopwatch();
        stopwatch.Start();

        // Send request
        Console.WriteLine($"Request Sending request [{request.RequestId}] at {DateTime.UtcNow:HH:mm:ss}");
        var response = await SendRequest(request);

        if (response.IsSuccessStatusCode)
        {
            Console.WriteLine($"Request [{request.RequestId}] Completed successfully at {DateTime.UtcNow:HH:mm:ss}, Time taken: {stopwatch.Elapsed.TotalSeconds} sec");

            var result = await response.Content.ReadAsByteArrayAsync();
            return new ApiResult(request.RequestId, result);
        }

        // Handle Too Many Requests Retry Logic
        if (response.StatusCode == System.Net.HttpStatusCode.TooManyRequests && 
            response.Headers.Contains("X-RateLimit-ResetIn") && 
            double.TryParse(response.Headers.GetValues("X-RateLimit-ResetIn").FirstOrDefault(), out var resetIn))
        {
            Console.WriteLine($"Request [{request.RequestId}] Received Too Many Requests. Retrying in {resetIn} sec...");
            await Task.Delay(TimeSpan.FromSeconds(resetIn));
            return await Remove(request);        
        }

        // Unexpected Error     
        Console.WriteLine($"Request [{request.RequestId}] Failed at {DateTime.UtcNow:HH:mm:ss}, " +
            $"Status code {response.StatusCode}, Message {response.ReasonPhrase}, " +
            $"Content : {await response.Content.ReadAsStringAsync()}");

        return new ApiResult(request.RequestId, null);
    }

    public static async Task<List<ApiResult>> RemoveMany(List<ApiRequest> apiRequests)
    {
        var semaphore = new SemaphoreSlim(maxParallelRequests); // Limit concurrent requests
        var tasks = apiRequests.Select(async request =>
        {
            await semaphore.WaitAsync();
            try
            {
                return await Remove(request);
            }
            finally
            {
                semaphore.Release();
            }
        });

        return (await Task.WhenAll(tasks)).ToList();
    }

    private static async Task<HttpResponseMessage> SendRequest(ApiRequest apiRequest)
    {
        var jsonContent = apiRequest.Config != null ? JsonConvert.SerializeObject(apiRequest.Config) : null;

        using (var client = new HttpClient())
        {
            // Add the authorization token and accept header
            client.DefaultRequestHeaders.Add("X-Key", userToken);
            client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));

            using (var content = new MultipartFormDataContent())
            {
                // Open the file to send it as a stream
                var fileContent = new ByteArrayContent(File.ReadAllBytes(apiRequest.SourceImagePath));
                fileContent.Headers.ContentType = new MediaTypeHeaderValue("application/octet-stream");

                // Add the file content to the multipart form
                content.Add(fileContent, "image", Path.GetFileName(apiRequest.SourceImagePath));

                // Add the serialized JSON configuration to the multipart form
                if (jsonContent != null)
                {
                    var jsonContentString = new StringContent(jsonContent, Encoding.UTF8, "application/json");
                    content.Add(jsonContentString, "config");
                }

                // Send the request
                return await client.PostAsync(apiUrl, content);
            }
        }
    }
}
