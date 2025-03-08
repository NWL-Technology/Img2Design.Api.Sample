// index.js
import fs from "fs";
import path from "path";
import { v4 as uuidv4 } from "uuid";
import { ApiRequest } from "./models/apiRequest.js";
import { ApiConfigRequest } from "./models/apiConfigRequest.js";
import { removeMany, remove } from "./background.js";

const outputDir = "outputs";
if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir);
}

const imagePath = "sample/img_sample.jpg";

////simple background removal without background
const apiRequest1 = new ApiRequest({
  requestId: uuidv4(),
  sourceImagePath: imagePath,
});
/*
remove(apiRequest1).then((result) => {
  saveFile(result);
});
*/
//////simple background removal with a image background blur
const apiRequest2 = new ApiRequest({
  requestId: uuidv4(),
  sourceImagePath: imagePath,
  config: new ApiConfigRequest({
    bgImageUrl:
      "https://github.com/makccr/wallpapers/blob/master/wallpapers/abstract/lucas-benjamin-R79qkPYvrcM-unsplash.jpg?raw=true",
    bgImageBlurLevel: 2,
    elementLocation: "center",
  }),
});
/*
remove(apiRequest2).then((result) => {
  saveFile(result);
});
*/
///multiple background removal with random background colors
const apiRequests = Array.from(
  { length: 53 },
  (_, i) =>
    new ApiRequest({
      requestId: i.toString(),
      sourceImagePath: imagePath,
      config: new ApiConfigRequest({
        bgColor: getRandomBackgroundColor(),
        elementLocation: "center",
      }),
    })
);

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
