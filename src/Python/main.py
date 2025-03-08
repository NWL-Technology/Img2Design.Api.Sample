import os
import time
import asyncio
import uuid
from models import ApiConfigRequest, ApiRequest, ApiResult
from background import Background

OUTPUT_DIR = "./outputs"
os.makedirs(OUTPUT_DIR, exist_ok=True)

IMAGE_PATH = "./sample/img_sample.jpg"

def get_random_background_color():
    return "#{:06X}".format(uuid.uuid4().int & 0xFFFFFF)

async def main():
    image_path = IMAGE_PATH
    api_requests = [
        ApiRequest(request_id=str(i), source_image_path=image_path, config=ApiConfigRequest(
            bg_color=get_random_background_color(),
            element_location="center"
        ))
        for i in range(50)  # Example: 52 requests
    ]

    batch_size = Background.MAX_PARALLEL_REQUESTS
    results = []

    for i in range(0, len(api_requests), batch_size):
        batch = api_requests[i:i + batch_size]
        batch_results = await Background.remove_many(batch)
        results.extend(batch_results)

    for result in results:
        if result.image_content:
            output_path = f"{OUTPUT_DIR}/background_removed_{result.request_id}.png"

            with open(output_path, "wb") as f:
                f.write(result.image_content)
            print(f"Saved: {output_path}")
        else:
            print(f"Error processing request {result.request_id}")

# Run the async function
asyncio.run(main())