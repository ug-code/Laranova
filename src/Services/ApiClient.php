<?php

namespace Laranova\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ApiClient
{
    private const int DEFAULT_TIMEOUT = 30;
    private const int DEFAULT_CONNECT_TIMEOUT = 10;

    public function __construct(
        private readonly int $timeout = self::DEFAULT_TIMEOUT,
        private readonly int $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT,
    ) {}

    public function send(string $method, string $url, array $headers = [], ?string $body = null): array
    {
        $startTime = microtime(true);

        try {
            $options = [
                'timeout' => $this->timeout,
                'connect_timeout' => $this->connectTimeout,
                'allow_redirects' => true,
                'verify' => false,
            ];

            if ($body !== null && $body !== '') {
                $options['body'] = $body;
            }

            $response = Http::withHeaders($headers)
                ->withOptions($options)
                ->send(strtoupper($method), $url);

            $duration = (microtime(true) - $startTime) * 1000;

            return [
                'status' => $response->status(),
                'headers' => $this->normalizeHeaders($response->headers()),
                'body' => $response->body(),
                'duration' => round($duration, 2),
            ];
        } catch (ConnectionException $e) {
            return $this->errorResponse($e->getMessage(), $startTime, 0);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), $startTime);
        }
    }

    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $key => $values) {
            $normalized[$key] = count($values) === 1 ? $values[0] : $values;
        }

        return $normalized;
    }

    private function errorResponse(string $message, float $startTime, int $status = 500): array
    {
        $duration = (microtime(true) - $startTime) * 1000;

        return [
            'status' => $status,
            'headers' => [],
            'body' => $message,
            'duration' => round($duration, 2),
            'error' => true,
        ];
    }
}
