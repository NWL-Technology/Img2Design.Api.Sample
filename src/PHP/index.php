<?php
require 'vendor/autoload.php';
require './models/ApiConfigRequest.php';
require './models/ApiRequest.php';
require './models/ApiResult.php';
require 'Background.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Exception\RequestException;

// Define output directory
$outputDir = 'outputs';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true); // Create directory if it does not exist
}

// Initialize Guzzle Client
$client = new Client(['timeout' => 100.0]);

$batchSize = 7; // Max parallel requests
$totalRequests = 52;
$results = [];

$imagePath = 'sample/img_sample.jpg';

// Generate API Requests
$apiRequests = [];
for ($i = 0; $i < $totalRequests; $i++) {
    $apiRequests[$i] = new ApiRequest();
    $apiRequests[$i]->requestId = (string) $i;
    $apiRequests[$i]->sourceImagePath = $imagePath;
    $apiRequests[$i]->config = new ApiConfigRequest();
    $apiRequests[$i]->config->bgColor = sprintf("#%06X", mt_rand(0, 0xFFFFFF));
    $apiRequests[$i]->config->elementLocation = 'center';
}

echo "Processing $totalRequests requests in parallel with concurrency limit of $batchSize...\n";

// Create Guzzle Pool with parallel execution and file saving
$pool = new Pool($client, array_map(function ($request) use ($client, $outputDir) {
    return function () use ($client, $request, $outputDir) {
        return Background::removeAsync($client, $request)
            ->then(
                function ($result) use ($request, $outputDir) {
                    // Ensure the directory still exists before writing
                    if (!is_dir($outputDir)) {
                        mkdir($outputDir, 0777, true);
                    }

                    $filePath = "$outputDir/background_removed{$result->requestId}.png";
                    file_put_contents($filePath, $result->imageContent);

                    echo "[{$request->requestId}] Saved: $filePath\n";
                },
                function ($exception) use ($request) {
                    echo "[{$request->requestId}] Request failed: " . $exception->getMessage() . "\n";
                }
            );
    };
}, $apiRequests), [
    'concurrency' => $batchSize,
]);

// Wait for all requests to complete
Utils::settle($pool->promise())->wait();

echo "All requests processed!\n";
?>