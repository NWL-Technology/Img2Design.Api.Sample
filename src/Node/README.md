# Img 2 Design - WebAPI Background Removal - Sample Node.js Code

## Installation

1. Clone the repository:
   ```sh
   git clone https://github.com/NWL-Technology/Img2Design.Api.Sample.git
   cd Img2Design.Api.Sample/src/Node
   ```
2. Install dependencies:
   ```sh
   npm install
   ```

## Configuration

- Update `background.js` with your API key:
  ```javascript
  const userToken = "your_user_token_here";
  ```
- Place a sample image inside the `sample/` directory, e.g., `sample/img_sample.jpg`.

## Running the Project

To start the processing, run:

```sh
node index.js
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

### `apiConfigRequest.js`

Handles optional configurations for API requests.

### `apiRequest.js`

Defines the request format for sending images to the API.

### `apiResult.js`

Handles the API response data.

### `background.js`

Manages the API communication, sending requests, and handling responses.

### `index.js`

Entry point that initializes API requests and saves processed images.

---

## License

This project is licensed under the MIT License.
