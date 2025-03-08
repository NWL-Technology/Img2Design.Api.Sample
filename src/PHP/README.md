# Img 2 Design - WebAPI Background Removal - Sample PHP Code

## Installation

1. Clone the repository:
   ```sh
   git clone https://github.com/your-repo/image-processing-php.git
   cd src/Php
   ```
2. Install dependencies (Guzzle HTTP Client):
   ```sh
   composer install
   ```

## Configuration

- Update `Background.php` with your API key:
  ```php
  private static $userToken = "your_user_token_here";
  ```
- Place a sample image inside the `sample/` directory, e.g., `sample/img_sample.jpg`.

## Running the Project

To start the processing, run:

```sh
php index.php
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

### `ApiConfigRequest.php`

Handles optional configurations for API requests.

### `ApiRequest.php`

Defines the request format for sending images to the API.

### `ApiResult.php`

Handles the API response data.

### `Background.php`

Manages the API communication, sending requests, and handling responses.

### `index.php`

Entry point that initializes API requests and saves processed images.

---

## License

This project is licensed under the MIT License.