# models.py
from __future__ import annotations

import json
import uuid
from dataclasses import dataclass, field, asdict
from enum import Enum
from typing import Optional, Dict, Any


# ---------- Enums ----------
class OutputFormat(str, Enum):
    Webp = "Webp"
    Jpeg = "Jpeg"
    Png = "Png"


class RenderMode(str, Enum):
    Plain = "Plain"                   # keep aspect ratio; no bg manipulation
    RemoveBackground = "RemoveBackground"  # subject cut-out; optional bg


class ElementPositionSimple(str, Enum):
    Auto = "Auto"            # default: Plain => None; RemoveBackground => Center
    None_ = "None"           # keep aspect ratio
    Center = "Center"        # square crop centered
    BottomCenter = "BottomCenter"  # square crop bottom-center (ignored in Plain)


# ---------- Data classes ----------
@dataclass
class BackgroundOptions:
    use_same_image: Optional[bool] = None
    color: Optional[str] = None
    image_url: Optional[str] = None
    image_base64: Optional[str] = None
    blur: Optional[int] = None  # 0..4

    def to_dict(self) -> Dict[str, Any]:
        data = {
            "useSameImage": self.use_same_image,
            "color": self.color,
            "imageUrl": self.image_url,
            "imageBase64": self.image_base64,
            "blur": self.blur,
        }
        # drop None
        return {k: v for k, v in data.items() if v is not None}


@dataclass
class ApiConfigRequest:
    # Behavior
    mode: RenderMode = RenderMode.RemoveBackground
    position: ElementPositionSimple = ElementPositionSimple.Auto

    # Look & feel
    zoom: float = 1.0      # 0.5..1 (use decimals; e.g., 0.95)
    round: float = 0.0     # allowed by samples to be values like 1, 15, 30

    # Output
    width: Optional[int] = None
    height: Optional[int] = None
    format: OutputFormat = OutputFormat.Webp

    # Background
    background: BackgroundOptions = field(default_factory=BackgroundOptions)

    def to_dict(self) -> Dict[str, Any]:
        data = {
            "mode": self.mode.value,
            "position": self._serialize_position(self.position),
            "zoom": self.zoom,
            "round": self.round,
            "width": self.width,
            "height": self.height,
            "format": self.format.value,
            "background": self.background.to_dict(),
        }
        # drop None and empty dicts
        return {
            k: v
            for k, v in data.items()
            if v is not None and (not isinstance(v, dict) or len(v) > 0)
        }

    @staticmethod
    def _serialize_position(pos: ElementPositionSimple) -> str:
        # The enum name "None_" maps to "None" for wire format
        return "None" if pos is ElementPositionSimple.None_ else pos.value

    def to_json(self) -> str:
        return json.dumps(self.to_dict(), indent=2)


@dataclass
class ApiRequest:
    source_image_path: str
    config: Optional[ApiConfigRequest] = None
    request_id: str = field(default_factory=lambda: str(uuid.uuid4()))

    def to_dict(self) -> Dict[str, Any]:
        data = {
            "requestId": self.request_id,
            "sourceImagePath": self.source_image_path,
            "config": self.config.to_dict() if self.config else None,
        }
        return {k: v for k, v in data.items() if v is not None}

    def to_json(self) -> str:
        return json.dumps(self.to_dict(), indent=2)


@dataclass
class ApiResult:
    request_id: str
    image_content: bytes
