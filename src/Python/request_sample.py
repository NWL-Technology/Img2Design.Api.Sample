# request_sample.py
from __future__ import annotations

from models import (
    ApiRequest,
    ApiConfigRequest,
    BackgroundOptions,
    OutputFormat,
    RenderMode,
    ElementPositionSimple,
)

class RequestSample:
    @staticmethod
    def req_remove_bg_only(image_path: str) -> ApiRequest:
        return ApiRequest(
            request_id="remove-bg-only",
            source_image_path=image_path,
            config=None,
        )

    @staticmethod
    def req_plain_rounded_300_width(image_path: str) -> ApiRequest:
        return ApiRequest(
            request_id="plain-rounded-300-width-webp",
            source_image_path=image_path,
            config=ApiConfigRequest(
                mode=RenderMode.Plain,
                position=ElementPositionSimple.Center,
                zoom=95,         # normalized (95%)
                round=1,           # circle when square in your samples
                width=300,
                format=OutputFormat.Webp,
            ),
        )

    @staticmethod
    def req_removebg_none_90zoom_15round_500square_webp_blurred_bg(image_path: str) -> ApiRequest:
        return ApiRequest(
            request_id="removebg-none-90zoom-15round-500square-webp-blurred-bg",
            source_image_path=image_path,
            config=ApiConfigRequest(
                mode=RenderMode.RemoveBackground,
                position=ElementPositionSimple.None_,
                zoom=90,         # normalized (90%)
                round=15,
                width=500,
                height=500,
                format=OutputFormat.Webp,
                background=BackgroundOptions(
                    use_same_image=True,
                    blur=3,
                ),
            ),
        )

    @staticmethod
    def req_removebg_width_400(image_path: str) -> ApiRequest:
        return ApiRequest(
            request_id="removebg-400-width-auto",
            source_image_path=image_path,
            config=ApiConfigRequest(
                mode=RenderMode.RemoveBackground,
                position=ElementPositionSimple.Auto,
                width=400,
            ),
        )

    @staticmethod
    def req_removebg_auto_90zoom_30round_webp_color_bg(image_path: str) -> ApiRequest:
        return ApiRequest(
            request_id="removebg-auto-90zoom-30round-webp-neutral-bg",
            source_image_path=image_path,
            config=ApiConfigRequest(
                mode=RenderMode.RemoveBackground,
                position=ElementPositionSimple.Auto,
                zoom=90,
                round=30,
                width=350,
                height=None,
                format=OutputFormat.Webp,
                background=BackgroundOptions(
                    use_same_image=False,
                    color="#F6F8FA",
                    blur=0,
                ),
            ),
        )

    @staticmethod
    def req_plain_center_75zoom_jpeg(image_path: str) -> ApiRequest:
        return ApiRequest(
            request_id="plain-center-75zoom-jpeg",
            source_image_path=image_path,
            config=ApiConfigRequest(
                mode=RenderMode.Plain,
                position=ElementPositionSimple.Center,
                zoom=75,     # normalized (75%)
                round=0,
                width=400,
                height=None,
                format=OutputFormat.Jpeg,
            ),
        )
