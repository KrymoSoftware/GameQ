<?php

/**
 * This file is part of GameQ.
 *
 * GameQ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GameQ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 */

namespace GameQ\Protocols;

use GameQ\Protocol;
use GameQ\Server;
use JsonException;

/**
 * Shared implementation for official HTTPS game-server directories.
 *
 * @author Sascha Greuel <sascha@softcreatr.de>
 */
abstract class OfficialDirectory extends Protocol
{
    protected string $transport = self::TRANSPORT_TCP;

    /** @var array<string, array{expires: int, response: mixed}> */
    private static array $directoryResponses = [];

    /** @var array<string, mixed>|null */
    protected ?array $serverData = null;

    public function beforeSend(Server $server): void
    {
        $response = array_key_exists('directory_response', $this->options)
            ? $this->options['directory_response']
            : $this->loadDirectory();

        $this->serverData = $this->findServer($response, $server);
    }

    abstract protected function directoryUrl(): string;

    /** @return array<string, mixed>|null */
    abstract protected function findServer(mixed $response, Server $server): ?array;

    private function loadDirectory(): mixed
    {
        $url = $this->directoryUrl();
        $cacheTtl = max(0, $this->normalizeInteger($this->options['directory_cache_ttl'] ?? 30, 30));

        $cachedResponse = self::$directoryResponses[$url] ?? null;

        if ($cacheTtl > 0 && $cachedResponse !== null && $cachedResponse['expires'] >= time()) {
            return $cachedResponse['response'];
        }

        $handle = curl_init($url);

        if ($handle === false) {
            return null;
        }

        $timeout = max(1, $this->normalizeInteger($this->options['http_timeout'] ?? 5, 5));
        $maximumLength = 16 * 1024 * 1024;
        $response = '';
        curl_setopt_array($handle, [
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_MAXFILESIZE => $maximumLength,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_USERAGENT => 'GameQ',
            CURLOPT_WRITEFUNCTION => static function ($handle, string $chunk) use (&$response, $maximumLength): int {
                if (strlen($response) + strlen($chunk) > $maximumLength) {
                    return 0;
                }

                $response .= $chunk;

                return strlen($chunk);
            },
        ]);
        $success = curl_exec($handle);
        $status = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

        if ($success !== true || $status !== 200) {
            return null;
        }

        try {
            $decoded = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

            if ($cacheTtl > 0) {
                self::$directoryResponses[$url] = [
                    'expires' => time() + $cacheTtl,
                    'response' => $decoded,
                ];
            }

            return $decoded;
        } catch (JsonException) {
            return null;
        }
    }
}
