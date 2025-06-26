<?php

namespace App\Core;

class Http
{
    /**
     * Make a GET request to a URL.
     */
    public static function get(string $url, array $headers = []): string
    {
        return self::request('GET', $url, null, $headers);
    }

    /**
     * Make a POST request to a URL with data.
     */
    public static function post(string $url, array $data = [], array $headers = []): string
    {
        return self::request(
            'POST',
            $url,
            json_encode($data),
            array_merge(['Content-Type: application/json'], $headers)
        );
    }

    /**
     * Generic request handler using cURL.
     */
    protected static function request(string $method, string $url, $body = null, array $headers = []): string
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER    => $headers,
            CURLOPT_POSTFIELDS    => $body,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return json(['error' => curl_error($ch)], 500);
        }

        curl_close($ch);
        return $response;
    }
}
