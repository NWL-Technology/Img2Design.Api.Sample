import axios from "axios";
import fs from "fs";
import FormData from "form-data";
import PQueue from "p-queue";
import { ApiResult } from "./models/apiResult.js";

const apiUrl = "https://api.img2design.io/api/theme/convert";
const userToken ="your_user_token_here"; // Replace with your actual token

const queue = new PQueue({
  concurrency: 7, // up to 7 requests running in parallel
  intervalCap: 20, // limit to 20 requests per minute
  interval: 60_000, // interval = 60,000 ms = 1 minute
});

export async function remove(request, attempt = 1) {
  console.log(
    `[${request.requestId}] - Attempt ${attempt} - Starting request at ${new Date().toISOString()}`
  );

  try {
    const startTime = Date.now();

    const response = await sendRequest(request);

    const endTime = Date.now();
    const elapsedSeconds = ((endTime - startTime) / 1000).toFixed(2);

    console.log(
      `[${request.requestId}] - Completed successfully with status ${response.status} in ${elapsedSeconds} sec`
    );
    return new ApiResult(request.requestId, response.data);
  } catch (error) {
    if (error.response) {
      const status = error.response.status;

      if (status === 429 && error.response.headers["x-ratelimit-resetin"]) {
        // Extract retry time from headers
        const resetInSeconds = parseFloat(error.response.headers["x-ratelimit-resetin"]) || 10;

        console.warn(
          `[${request.requestId}] - 429 Too Many Requests. Retrying in ${resetInSeconds} seconds...`
        );

        await new Promise((resolve) => setTimeout(resolve, resetInSeconds * 1000));

        // Retry request recursively
        return remove(request, attempt + 1);
      }

      console.error(
        `[${request.requestId}] - Failed with status ${status}`,
        `Message: ${error.response.statusText}`,
        `Content: ${JSON.stringify(error.response.data)}`
      );
    } else {
      // No response => network or other error
      console.error(`[${request.requestId}] - Error: ${error.message}`);
    }

    return new ApiResult(request.requestId, null);
  }
}

export async function removeMany(apiRequests) {
  const tasks = apiRequests.map((request) =>
    queue.add(() => remove(request))
  );

  return Promise.all(tasks);
}

async function sendRequest(apiRequest) {
  const formData = new FormData();
  formData.append("image", fs.createReadStream(apiRequest.sourceImagePath), {
    filename: apiRequest.sourceImagePath.split("/").pop(),
    contentType: "image/jpeg",
  });

  if (apiRequest.config) {
    formData.append("config", JSON.stringify(apiRequest.config));
  }

  return axios.post(apiUrl, formData, {
    headers: {
      "X-Key": userToken,
      ...formData.getHeaders(),
    },
    responseType: "arraybuffer", // Ensure response is handled as binary data
  });
}
