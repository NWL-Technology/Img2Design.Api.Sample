// apiConfigRequest.js
export class ApiConfigRequest {
  constructor({
    elementLocation,
    zoom = 0.8,
    bgImageBase64,
    bgImageBlurLevel,
    bgImageUrl,
    bgColor,
  }) {
    this.elementLocation = elementLocation; // "center", "bottomCenter", "aspectRatio"
    this.zoom = zoom;
    this.bgImageBase64 = bgImageBase64;
    this.bgImageBlurLevel = bgImageBlurLevel; // possible values 1, 2, 3
    this.bgImageUrl = bgImageUrl;
    this.bgColor = bgColor;
  }
}
