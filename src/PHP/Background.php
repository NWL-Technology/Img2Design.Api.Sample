<?php

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;

class Background
{
    private static $apiUrl = "https://api.img2design.io/api/theme/convert";
    private static $userToken = "your_user_token_here"; // Replace with your actual token
    private static $maxParallelRequests = 7;
    private static ?Client $client = null; // Ensure it's nullable and initialized properly
    private static $retryPromise = null; // Shared promise for retries

    private static function getClient()
    {
        if (self::$client === null) {
            self::$client = new Client([
                'timeout' => 100.0, // Set timeout for requests
                'verify' => false,
            ]);
        }
        return self::$client;
    }

    public static function removeAsync(Client $client, ApiRequest $request, $attempt = 1)
{
    $multipart = [
        [
            'name' => 'image',
            'contents' => fopen($request->sourceImagePath, 'r'),
            'filename' => basename($request->sourceImagePath),
        ]
    ];

    if (!empty($request->config)) {
        $multipart[] = [
            'name' => 'config',
            'contents' => json_encode($request->config),
            'headers' => ['Content-Type' => 'application/json'],
        ];
    }

    $options = [
        'headers' => ['X-Key' => self::$userToken],
        'multipart' => $multipart,
        'verify' => false,
    ];

    echo "[{$request->requestId}] Sending request asynchronously...\n";

    return $client->postAsync(self::$apiUrl, $options)
        ->then(
            function ($response) use ($request) {
                echo "[{$request->requestId}] Completed successfully!\n";
                return new ApiResult($request->requestId, $response->getBody()->getContents());
            }
        )
        ->otherwise(
            function ($exception) use ($client, $request, $attempt) {
                if ($exception instanceof RequestException) {
                    $response = $exception->getResponse();

                    if ($response && $response->getStatusCode() === 429) {
                        $retryAfter = self::getRateLimitResetIn($response);

                        echo "[{$request->requestId}] Received 429 Too Many Requests. Retrying in $retryAfter seconds (Attempt $attempt)...\n";

                        return self::delayedRetry($client, $request, $attempt, $retryAfter);
                    }
                }

                echo "[{$request->requestId}] Request failed: " . $exception->getMessage() . "\n";
                return new ApiResult($request->requestId, null);
            }
        );
    }
    
    private static function delayedRetry(Client $client, ApiRequest $request, $attempt, $retryAfter)
    {
        if (self::$retryPromise === null) {
            echo "[{$request->requestId}] Initiating global retry wait for $retryAfter seconds...\n";

            // The first request starts the sleep and creates the shared promise
            self::$retryPromise = \GuzzleHttp\Promise\Utils::task(function () use ($retryAfter) {
                sleep($retryAfter);
                echo "Retry timer finished. Resuming requests...\n";
                self::$retryPromise = null; // Reset after waiting
            });
        } else {
            echo "[{$request->requestId}] Waiting for the global retry timer to finish...\n";
        }

        // Attach all waiting requests to the same retry promise
        return self::$retryPromise->then(function () use ($client, $request, $attempt) {
            return self::removeAsync($client, $request, $attempt + 1);
        });
    }


    public static function remove(ApiRequest $request, $attempt = 1)
    {
        try {
            $multipart = [
                [
                    'name' => 'image',
                    'contents' => fopen($request->sourceImagePath, 'r'),
                    'filename' => basename($request->sourceImagePath),
                ]
            ];

            // Only include config if it's provided and not null
            if (!empty($request->config)) {
                $multipart[] = [
                    'name' => 'config',
                    'contents' => json_encode($request->config),
                    'headers' => ['Content-Type' => 'application/json'],
                ];
            }

            $options = [
                'headers' => ['X-Key' => self::$userToken],
                'multipart' => $multipart,
            ];

            echo "[{$request->requestId}] Attempt $attempt - Sending request...\n";
            $response = self::getClient()->post(self::$apiUrl, $options);

            echo "[{$request->requestId}] Completed with status: " . $response->getStatusCode() . "\n";
            return new ApiResult($request->requestId, $response->getBody()->getContents());
        } catch (RequestException $e) {
            if ($e->getResponse() && $e->getResponse()->getStatusCode() === 429) {
                $retryAfter = self::getRateLimitResetIn($e->getResponse());

                echo "[{$request->requestId}] Received 429. Retrying in $retryAfter seconds...\n";
                sleep($retryAfter);

                return self::remove($request, $attempt + 1); // Recursive retry
            }

            // Log error details
            echo "[{$request->requestId}] Request failed: " . $e->getMessage() . "\n";
            return new ApiResult($request->requestId, null);
        }
    }

    public static function removeMany(array $apiRequests)
    {
        $client = self::getClient(); // Ensure client is properly initialized
        $requests = function ($apiRequests) {
            foreach ($apiRequests as $request) {
                yield function () use ($request) {
                    return self::remove($request);
                };
            }
        };

        $results = [];
        $pool = new Pool($client, $requests($apiRequests), [
            'concurrency' => self::$maxParallelRequests,
            'fulfilled' => function ($response, $index) use (&$results, $apiRequests) {
                $results[] = new ApiResult($apiRequests[$index]->requestId, $response);
            },
            'rejected' => function ($reason, $index) use (&$results, $apiRequests) {
                echo "[{$apiRequests[$index]->requestId}] Request failed: " . $reason->getMessage() . "\n";
                $results[] = new ApiResult($apiRequests[$index]->requestId, null);
            },
        ]);

        Utils::settle($pool->promise())->wait();
        return $results;
    }

    private static function getRateLimitResetIn($response)
    {
        $retryAfter = $response->getHeader('X-RateLimit-ResetIn');
        return isset($retryAfter[0]) ? (int) $retryAfter[0] : 10; // Default to 10 seconds
    }
}
