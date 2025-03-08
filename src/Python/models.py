import uuid
from typing import Optional
import json

class ApiConfigRequest:
    def __init__(
        self,
        element_location: str = "center",  # Default value
        zoom: float = 0.8,  # Default value
        bg_image_base64: Optional[str] = None,
        bg_image_blur_level: Optional[int] = None,  # Possible values: 1, 2, 3
        bg_image_url: Optional[str] = None,
        bg_color: Optional[str] = None
    ):
        self.element_location = element_location
        self.zoom = zoom
        self.bg_image_base64 = bg_image_base64
        self.bg_image_blur_level = bg_image_blur_level
        self.bg_image_url = bg_image_url
        self.bg_color = bg_color

    def to_dict(self):
        """Convert the object to a dictionary with camelCase keys, excluding None values."""
        return {
            "elementLocation": self.element_location,
            "zoom": self.zoom,
            "bgImageBase64": self.bg_image_base64,
            "bgImageBlurLevel": self.bg_image_blur_level,
            "bgImageUrl": self.bg_image_url,
            "bgColor": self.bg_color,
        }

    def to_json(self):
        """Convert the object to a JSON string, excluding None values."""
        return json.dumps({k: v for k, v in self.to_dict().items() if v is not None}, indent=4)


class ApiRequest:
    def __init__(self, request_id=None, source_image_path=None, config=None):
        self.request_id = request_id if request_id else str(uuid.uuid4())
        self.source_image_path = source_image_path
        self.config = config if config else None


class ApiResult:
    def __init__(self, request_id, image_content):
        self.request_id = request_id
        self.image_content = image_content
