import {
  ApiConfigRequest,
  BackgroundOptions,
  OutputFormat,
  RenderMode,
  ElementPositionSimple,
} from "./models/apiConfigRequest.js";

// Simple ApiRequest model 
export class ApiRequest {
  constructor({ requestId, sourceImagePath, config = null }) {
    this.requestId = requestId;
    this.sourceImagePath = sourceImagePath;
    this.config = config;
  }
}

export const reqRemoveBgOnly = (imagePath) =>
  new ApiRequest({
    requestId: "remove-bg-only",
    sourceImagePath: imagePath,    
  });

export const reqPlainRounded300Width = (imagePath) =>
  new ApiRequest({
    requestId: "plain-rounded-300-width-webp",
    sourceImagePath: imagePath,
    config: new ApiConfigRequest({
      mode: RenderMode.Plain,
      position: ElementPositionSimple.Center,
      zoom: 95, 
      round: 1,
      width: 300,
      format: OutputFormat.Webp,
    }),
  });

export const reqRemovebgNone90zoom15Round500SquareWebpBlurredBg = (imagePath) =>
  new ApiRequest({
    requestId: "removebg-none-90zoom-15round-500square-webp-blurred-bg",
    sourceImagePath: imagePath,
    config: new ApiConfigRequest({
      mode: RenderMode.RemoveBackground,
      position: ElementPositionSimple.None,
      zoom: 90,  
      round: 15,
      width: 500,
      height: 500,
      format: OutputFormat.Webp,
      background: new BackgroundOptions({
        useSameImage: true,
        blur: 3,
      }),
    }),
  });

export const reqRemovebgWidth400 = (imagePath) =>
  new ApiRequest({
    requestId: "removebg-400-width-auto",
    sourceImagePath: imagePath,
    config: new ApiConfigRequest({
      mode: RenderMode.RemoveBackground,
      position: ElementPositionSimple.Auto,
      width: 400,
    }),
  });

export const reqRemovebgAuto90Zoom30RoundWebpColorBg = (imagePath) =>
  new ApiRequest({
    requestId: "removebg-auto-90zoom-30round-webp-neutral-bg",
    sourceImagePath: imagePath,
    config: new ApiConfigRequest({
      mode: RenderMode.RemoveBackground,
      position: ElementPositionSimple.Auto,
      zoom: 0.9,
      round: 30,
      width: 350,
      height: null,
      format: OutputFormat.Webp,
      background: new BackgroundOptions({
        useSameImage: false,
        color: "#F6F8FA",
        blur: 0,
      }),
    }),
  });

export const reqPlainCenter80ZoomJpeg = (imagePath) =>
  new ApiRequest({
    requestId: "plain-center-75zoom-jpeg",
    sourceImagePath: imagePath,
    config: new ApiConfigRequest({
      mode: RenderMode.Plain,
      position: ElementPositionSimple.Center,
      zoom: 75,
      round: 0,
      width: 400,
      height: null,
      format: OutputFormat.Jpeg,
    }),
  });
