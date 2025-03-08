import asyncio
import json
import ssl
import time
from models import ApiConfigRequest, ApiRequest, ApiResult
from aiohttp import ClientSession, ClientConnectionError, TCPConnector, ClientTimeout, FormData
from aiohttp.client_exceptions import ServerDisconnectedError, ClientResponseError

class Background:
    API_URL = "https://api.img2design.io/api/theme/convert"
    USER_TOKEN = "your_user_token_here"; # Replace with your actual token
    MAX_PARALLEL_REQUESTS = 7
    MAX_RETRIES = 3

    session = None  # Shared session for all requests
    semaphore = asyncio.Semaphore(MAX_PARALLEL_REQUESTS)  # Limit parallel execution

    @staticmethod
    async def init_session():
        """Initialize a persistent session to prevent connection issues."""
        if Background.session is None:
            ssl_context = ssl.create_default_context()
            connector = TCPConnector(limit_per_host=Background.MAX_PARALLEL_REQUESTS, ssl=ssl_context, keepalive_timeout=30)
            Background.session = ClientSession(connector=connector, timeout=ClientTimeout(total=60))

    @staticmethod
    async def close_session():
        """Ensure the session is closed after all requests are complete."""
        if Background.session is not None:
            await Background.session.close()
            Background.session = None

    @staticmethod
    async def remove(request, attempt=1):
        """Handles a single API request with proper retries and content streaming."""
        await Background.init_session()  # Ensure session is initialized

        async with Background.semaphore:  # Limit parallel execution
            for retry in range(Background.MAX_RETRIES):
                try:
                    print(f"[{request.request_id}] Sending request at {time.strftime('%H:%M:%S')} (Attempt {attempt})")
                    response, content = await Background.send_request(request)

                    if response.status == 200:
                        try:
                            print(f"[{request.request_id}] Completed successfully.")
                            return ApiResult(request.request_id, content)
                        except Exception as e:
                            print(f"[{request.request_id}] Error while reading response: {e}")
                            continue  # Retry the request

                    if response.status == 429:  # API Rate Limit
                        retry_after = float(response.headers.get("X-RateLimit-ResetIn", 10))
                        print(f"[{request.request_id}] Rate limited. Retrying in {retry_after} sec...")
                        await asyncio.sleep(retry_after)
                        continue  # Retry after waiting

                    print(f"[{request.request_id}] Failed, Status: {response.status}, Message: {response.reason}")
                    return ApiResult(request.request_id, None)

                except (ClientConnectionError, ServerDisconnectedError, ClientResponseError) as e:
                    wait_time = 2 ** retry  # Exponential backoff (2, 4, 8 sec)
                    print(f"[{request.request_id}] Connection Error. Retrying in {wait_time} sec... {str(e)}")
                    await asyncio.sleep(wait_time)

            print(f"[{request.request_id}] Connection failed after {Background.MAX_RETRIES} retries.")
            return ApiResult(request.request_id, None)

    @staticmethod
    async def remove_many(api_requests):
        """Manages multiple requests in parallel while ensuring all complete before closing the session."""
        await Background.init_session()

        tasks = [asyncio.create_task(Background.remove(req)) for req in api_requests]
        results = await asyncio.gather(*tasks)

        await Background.close_session()  # Close session only after all requests finish
        return results

    @staticmethod
    async def send_request(api_request):
        """Sends an API request using the shared session and returns a tuple of the response and content."""
        json_content = api_request.config.to_json() if api_request.config else None

        headers = {"X-Key": Background.USER_TOKEN}
        data = FormData()
        data.add_field("image", open(api_request.source_image_path, "rb"),
                       filename=api_request.source_image_path.split("/")[-1],
                       content_type="application/octet-stream")

        if json_content:
            data.add_field("config", json_content, content_type="application/json")

        async with Background.session.post(Background.API_URL, headers=headers, data=data) as response:
            content = await response.content.read() if response.ok else None
            return response, content