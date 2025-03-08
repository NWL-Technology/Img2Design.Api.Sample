// apiRequest.js
export class ApiRequest {
  constructor({ requestId, sourceImagePath, config }) {
    this.requestId = requestId || null;
    this.sourceImagePath = sourceImagePath;
    this.config = config || null;
  }
}
