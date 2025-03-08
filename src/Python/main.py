# main.py
import os
import asyncio
from pathlib import Path

from models import ApiRequest, ApiResult, OutputFormat  # OutputFormat optional (for filename ext)
from request_sample import RequestSample
from background import Background

OUTPUT_DIR = Path("./outputs")
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

IMAGE_PATH = "sample/img_sample.jpg"
IMAGE_CAR_PATH = "sample/img_sample_car.jpg"
IMAGE_FOOD_PATH = "sample/img_sample_food.jpg"
IMAGE_PERSON_PATH = "sample/img_sample_person.jpg"


def _ext_from_request(req: ApiRequest) -> str:
    """Best-effort filename extension based on requested format; defaults to png."""
    try:
        fmt = getattr(getattr(req, "config", None), "format", None)
        if fmt is None:
            return "png"
        mapping = {
            "Webp": "webp",
            "Jpeg": "jpg",
            "Png": "png",
        }
        # fmt may be enum or string
        key = getattr(fmt, "value", fmt)
        return mapping.get(key, "png")
    except Exception:
        return "png"


def save_file(result: ApiResult, req: ApiRequest) -> None:
    if not getattr(result, "image_content", None):
        print(f"[{result.request_id}] No image content.")
        return
    ext = _ext_from_request(req)
    out_path = OUTPUT_DIR / f"{result.request_id}.{ext}"
    with open(out_path, "wb") as f:
        f.write(result.image_content)
    print(f"[{result.request_id}] Saved: {out_path}")


async def remove(api_request: ApiRequest) -> ApiResult:
    """Single removal; delegates to Background.remove_many for consistency."""
    results = await Background.remove_many([api_request])
    return results[0]


async def remove_many(api_requests: list[ApiRequest]) -> list[ApiResult]:
    """Multiple removals; Background.remove_many should handle concurrency internally."""
    return await Background.remove_many(api_requests)


async def main():
    # ---- 1) simple background removal without background ----
    api_request1 = RequestSample.req_remove_bg_only(IMAGE_FOOD_PATH)
    result1 = await remove(api_request1)
    save_file(result1, api_request1)

    # ---- 1a) simple background removal with resize center square ----
    api_request1a = RequestSample.req_removebg_width_400(IMAGE_FOOD_PATH)
    result1a = await remove(api_request1a)
    save_file(result1a, api_request1a)

    # ---- 2) multiple background removals (like JS removeMany / PHP removeMany) ----
    api_requests = [
        RequestSample.req_plain_center_75zoom_jpeg(IMAGE_PATH),
        RequestSample.req_plain_rounded_300_width(IMAGE_PERSON_PATH),
        RequestSample.req_removebg_auto_90zoom_30round_webp_color_bg(IMAGE_CAR_PATH),
        RequestSample.req_removebg_none_90zoom_15round_500square_webp_blurred_bg(IMAGE_FOOD_PATH),
    ]

    print("Processing requests in parallel...")
    results = await remove_many(api_requests)

    # Save all
    for req, res in zip(api_requests, results):
        save_file(res, req)

    print("All requests processed!")


if __name__ == "__main__":
    asyncio.run(main())
