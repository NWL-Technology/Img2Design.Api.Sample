// apiConfigRequest.js

// Enums
export const OutputFormat = Object.freeze({
  Webp: "Webp",
  Jpeg: "Jpeg",
  Png: "Png",
});

export const RenderMode = Object.freeze({
  Plain: "Plain",
  RemoveBackground: "RemoveBackground",
});

export const ElementPositionSimple = Object.freeze({
  Auto: "Auto",
  None: "None",
  Center: "Center",
  BottomCenter: "BottomCenter",
});

// BackgroundOptions class
export class BackgroundOptions {
  constructor({
    useSameImage = null,
    color = null,
    imageUrl = null,
    imageBase64 = null,
    blur = null,
  } = {}) {
    this.useSameImage = useSameImage; // boolean?
    this.color = color;               // string hex
    this.imageUrl = imageUrl;         // string url
    this.imageBase64 = imageBase64;   // string base64
    this.blur = blur;                 // 0..4
  }
}

// ApiConfigRequest class
export class ApiConfigRequest {
  constructor({
    mode = RenderMode.RemoveBackground,
    position = ElementPositionSimple.Auto,

    // Look & feel
    zoom = 1.0,    // range 0.5..1
    round = 0.0,   // 0..1

    // Output
    width = null,
    height = null,
    format = OutputFormat.Webp,

    // Background
    background = new BackgroundOptions(),
  } = {}) {
    this.mode = mode;
    this.position = position;

    this.zoom = zoom;
    this.round = round;

    this.width = width;
    this.height = height;
    this.format = format;

    this.background = background;
  }
}
