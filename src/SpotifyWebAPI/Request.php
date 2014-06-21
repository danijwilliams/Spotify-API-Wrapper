<?php
namespace SpotifyWebAPI;

class Request
{
    const ACCOUNT_URL = 'https://accounts.spotify.com';
    const API_URL = 'https://api.spotify.com';

    /**
     * Make a request to the "account" endpoint.
     *
     * @param string $method The HTTP method to use.
     * @param string $uri The URI to request.
     * @param array $parameters Optional. Query parameters.
     * @param array $headers HTTP headers.
     *
     * @return array
     */
    public static function account($method, $uri, $parameters = array(), $headers = array())
    {
        return self::send($method, self::ACCOUNT_URL . $uri, $parameters, $headers);
    }

    /**
     * Make a request to the "api" endpoint.
     *
     * @param string $method The HTTP method to use.
     * @param string $uri The URI to request.
     * @param array $parameters Optional. Query parameters.
     * @param array $headers HTTP headers.
     *
     * @return array
     */
    public static function api($method, $uri, $parameters = array(), $headers = array())
    {
        return self::send($method, self::API_URL . $uri, $parameters, $headers);
    }

    /**
     * Make a request to Spotify.
     * You'll probably want to use one of the convenience methods instead.
     *
     * @param string $method The HTTP method to use.
     * @param string $uri The URL to request.
     * @param array $parameters Optional. Query parameters.
     * @param array $headers HTTP headers.
     *
     * @return array
     */
    public static function send($method, $url, $parameters = array(), $headers = array())
    {
        // Sometimes a JSON object is passed
        if (is_array($parameters) || is_object($parameters)) {
            $parameters = http_build_query($parameters);
        }

        $mergedHeaders = array();
        foreach ($headers as $key => $val) {
            $mergedHeaders[] = "$key: $val";
        }

        $options = array(
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => $mergedHeaders,
            CURLOPT_RETURNTRANSFER => true
        );

        if (strtoupper($method) == 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $parameters;
        } else {
            rtrim($url, '/');
            $url .= '/?' . $parameters;
        }

        $options[CURLOPT_URL] = $url;

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        list($headers, $body) = explode("\r\n\r\n", $response);

        return array(
            'body' => $body,
            'headers' => $headers
        );
    }
}
