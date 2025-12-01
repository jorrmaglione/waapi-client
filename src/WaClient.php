<?php

namespace Jorrmaglione\WaClient;

use RuntimeException;

/**
 *
 */
final class WaClient {
    /**
     * @var string
     */
    private string $token;
    /**
     * @var string
     */
    private string $baseUri;

    /**
     * @param string $token
     * @param string $baseUri
     */
    public function __construct(string $token, string $baseUri = 'https://waapi.app/api/v1/') {
        $this->token = $token;
        $this->baseUri = $baseUri;
    }

    /**
     * @param string     $method
     * @param string     $path
     * @param array|null $json
     *
     * @return array
     */
    public function request(string $method, string $path, array $json = null): array {
        $url = rtrim($this->baseUri, '/') . '/' . ltrim($path, '/');

        $curlHandler = curl_init($url);

        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        curl_setopt_array($curlHandler, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 200,
            CURLOPT_CONNECTTIMEOUT => 100
        ]);

        if ($json !== null)
            curl_setopt($curlHandler, CURLOPT_POSTFIELDS, json_encode($json, JSON_UNESCAPED_SLASHES));

        $body = curl_exec($curlHandler);

        $code = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);

        $errorString = curl_error($curlHandler);

        curl_close($curlHandler);

        if ($body === false || $code >= 400)
            throw new RuntimeException("WaAPI [$method] [$url] failed ($code): " . ($body ?: $errorString));

        $response = json_decode($body, true);

        if (!isset($response['status']) || $response['status'] !== 'success')
            throw new RuntimeException("WaAPI [$method] [$url] failed: " . $body);

        return $response;
    }

    /**
     * @param string $instanceId
     *
     * @return WaInstance
     */
    public function getInstance(string $instanceId): WaInstance {
        return new WaInstance($this, $instanceId);
    }
}