# Img 2 Design - WebAPI Background Removal - Sample C# Code

## Installation

1. **Clone the repository:**
   ```sh
   git clone https://github.com/NWL-Technology/Img2Design.Api.Sample.git
   cd Img2Design.Api.Sample/src/CSharp
   ```

2. **Open the project:**
   - Open the `.sln` file in **Visual Studio** or **Visual Studio Code**.

3. **Install dependencies:**
   - Ensure you have **.NET 6.0 or later** installed.
   - Restore NuGet packages by running:

   ```sh
   dotnet restore
   ```

## Configuration

- Update `Background.cs` with your API key:
  ```csharp
  
    private static readonly userToken = "your_user_token_here";
  ```
- Place a sample image inside the `sample/` directory, e.g., `sample/img_sample.jpg`.

## Running the Project

To start the processing, execute:
```sh
 dotnet run
```

This will send multiple requests to the API and save the processed images inside the `outputs/` directory.

## Output

Processed images will be saved in the `outputs/` directory with filenames in the format:

```
outputs/background_removed0.png
outputs/background_removed1.png
```

## Notes

- The API supports optional configurations such as background color and positioning.
- Ensure you have a valid API key before running the project.
- If the API rate limit is reached, retry after the specified time.

---

## Project Files

### `ApiConfigRequest.cs`
Handles optional configurations for API requests.

### `ApiRequest.cs`
Defines the request format for sending images to the API.

### `ApiResult.cs`
Handles the API response data.

### `Background.cs`
Manages the API communication, sending requests, and handling responses.

### `Program.cs`
Entry point that initializes API requests and saves processed images.

---

## License

This project is licensed under the MIT License.

