<?php
class ApiResult {
    public $requestId;
    public $imageContent;
    
    public function __construct($requestId, $imageContent) {
        $this->requestId = $requestId;
        $this->imageContent = $imageContent;
    }
}
