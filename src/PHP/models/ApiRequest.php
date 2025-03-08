<?php
class ApiRequest {
    public string $requestId;
    public string $sourceImagePath;
    public ?ApiConfigRequest $config;

    public function __construct(string $requestId, string $sourceImagePath, ?ApiConfigRequest $config = null) {
        $this->requestId = $requestId;
        $this->sourceImagePath = $sourceImagePath;
        $this->config = $config;
    }
}