<?php
require 'vendor/autoload.php';
require './models/ApiConfigRequest.php';
require './models/ApiRequest.php';
require './models/ApiResult.php';
require 'Background.php';
require 'RequestSample.php';

use GuzzleHttp\Client;
use GuzzleHttp\Pool;

// Define output directory
$outputDir = 'outputs';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true); // Create directory if it does not exist
}

// Initialize Guzzle Client
$client = new Client(['timeout' => 100.0]);

$batchSize = 3; // Max parallel requests
$results = [];

$imagePath = 'sample/img_sample.jpg';
$imageCarPath = "sample/img_sample_car.jpg";
$imageFoodPath = "sample/img_sample_food.jpg";
$imagePersonPath = "sample/img_sample_person.jpg";

// -------------------------------------------
// 1) simple background removal without background
// -------------------------------------------
$apiRequest1 = RequestSample::reqRemoveBgOnly($imageFoodPath);
remove($client, $apiRequest1)
    ->then(fn ($result) => saveFile($result, $outputDir))
    ->otherwise(fn ($e) => print "[{$apiRequest1->requestId}] Request failed: " . $e->getMessage() . "\n")
    ->wait();


// -------------------------------------------
// 1a) simple background removal with resize center square
// -------------------------------------------
$apiRequest1a = RequestSample::reqRemovebgWidth400($imageFoodPath);
remove($client, $apiRequest1a)
    ->then(fn ($result) => saveFile($result, $outputDir))
    ->otherwise(fn ($e) => print "[{$apiRequest1a->requestId}] Request failed: " . $e->getMessage() . "\n")
    ->wait();

// -------------------------------------------
// 2) multiple background removals (like JS removeMany)
// -------------------------------------------
$apiRequests = [
    RequestSample::reqPlainCenter80ZoomJpeg($imagePath),
    RequestSample::reqPlainRounded300Width($imagePersonPath),
    RequestSample::reqRemovebgAuto90Zoom30RoundWebpColorBg($imageCarPath),
    RequestSample::reqRemovebgNone90zoom15Round500SquareWebpBlurredBg($imageFoodPath),
];

echo "Processing requests in parallel with concurrency limit of {$batchSize}...\n";

removeMany($client, $apiRequests, $batchSize, $outputDir)->wait();

echo "All requests processed!\n";

// --- helpers ---
/**
 * Save file result to disk.
 * Expects $result to have ->requestId and ->imageContent (like your Background::removeAsync result).
 */
function saveFile($result, string $outputDir): void
{
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    // Use PNG like your original sample. If you have a format on $result, you can switch to it.
    $filePath = rtrim($outputDir, '/\\') . '/' . $result->requestId . '.png';
    file_put_contents($filePath, $result->imageContent);

    echo "[{$result->requestId}] Saved: {$filePath}\n";
}

/**
 * Single background removal (promise).
 */
function remove(Client $client, ApiRequest $request)
{
    return Background::removeAsync($client, $request);
}

/**
 * Multiple background removals with concurrency (promise).
 */
function removeMany(
    Client $client,
    array $apiRequests,
    int $concurrency,
    string $outputDir
) {
    $requests = array_map(function (ApiRequest $req) use ($client) {
        return function () use ($client, $req) {
            return Background::removeAsync($client, $req);
        };
    }, $apiRequests);

    $pool = new Pool($client, $requests, [
        'concurrency' => $concurrency,
        'fulfilled' => function ($result, $idx) use ($apiRequests, $outputDir) {
            // $result is the fulfilled value from removeAsync
            saveFile($result, $outputDir);
        },
        'rejected' => function ($reason, $idx) use ($apiRequests) {
            $req = $apiRequests[$idx] ?? null;
            $id  = $req?->requestId ?? "#{$idx}";
            $msg = $reason instanceof \Throwable ? $reason->getMessage() : (string)$reason;
            echo "[{$id}] Request failed: {$msg}\n";
        },
    ]);

    return $pool->promise();
}

?>