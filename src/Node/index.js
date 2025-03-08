// index.js
import fs from "fs";
import path from "path";
import { v4 as uuidv4 } from "uuid";
import { ApiRequest } from "./models/apiRequest.js";
import { ApiConfigRequest } from "./models/apiConfigRequest.js";
import { removeMany, remove } from "./background.js";
import * as req from './requestSample.js';

const outputDir = "outputs";
if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir);
}

const imagePath = "sample/img_sample.jpg";
const imageCarPath = "sample/img_sample_car.jpg";
const imageFoodPath = "sample/img_sample_food.jpg";
const imagePersonPath = "sample/img_sample_person.jpg";

////simple background removal without background
const apiRequest1 = req.reqRemoveBgOnly(imageFoodPath)
remove(apiRequest1).then((result) =>   saveFile(result));

////simple background removal with resize center square
const apiRequest1a = req.reqRemovebgWidth400(imageFoodPath)
remove(apiRequest1a).then((result) =>   saveFile(result));

///multiple background removal with random background colors
const apiRequests = [
  req.reqPlainCenter80ZoomJpeg(imagePath),
  req.reqPlainRounded300Width(imagePersonPath),
  req.reqRemovebgAuto90Zoom30RoundWebpColorBg(imageCarPath),
  req.reqRemovebgNone90zoom15Round500SquareWebpBlurredBg(imageFoodPath),
];
  
removeMany(apiRequests).then((results) => {
  results.forEach(saveFile);
});

function saveFile(apiResult) {
  const filePath = path.join(
    outputDir,
    `background_removed${apiResult.requestId || ""}.png`
  );
  if (!apiResult.imageContent) {
    console.error(
      `Image processing failed for request [${apiResult.requestId}]`
    );
    return;
  }
  fs.writeFileSync(filePath, apiResult.imageContent);
  console.log(`Background removed and saved as ${filePath}`);
}

function getRandomBackgroundColor() {
  return `#${Math.floor(Math.random() * 16777215).toString(16)}`;
}
