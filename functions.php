<?php

/**
 * @param string[] $headerLines
 */
function parse_headers(array $headerLines): array
{
    // could also use the built-in http_parse_headers()
    $requestInfo = [
        'method' => 'GET',
        'version' => '1.1',
        'scheme' => 'http',
        'host' => null,
        'port' => null,
        'uri' => null,
        'path' => null,
        'queryString' => null,
        'parsedQueryString' => [],
        'fragment' => null,
        'headers' => [],
        'cookies' => [],
        'error' => null,
    ];
    $headers = [];

    $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'];
    $versions = ['1.0', '1.1', '2.0'];

    $isFirstLine = true;
    foreach ($headerLines as $line) {

        echo "headerline: $line" . PHP_EOL;
        $matches = [];

        if ($isFirstLine) {
            if (preg_match('~^([A-U]+) (/.*) HTTP/([12\.]{1,3})\s+$~', $line, $matches) !== 1) {
                $requestInfo['error'] = 'Error parsing the first header line.';
                return $requestInfo;
            }

            if (!in_array($matches[1], $methods)) {
                $requestInfo['error'] = "Wrong HTTP method '$matches[1]'.";
                return $requestInfo;
            }

            if (!in_array($matches[3], $versions)) {
                $requestInfo['error'] = "Wrong HTTP protocol version '$matches[3]'.";
                return $requestInfo;
            }

            $requestInfo['method'] = $matches[1];
            $requestInfo['uri'] = $matches[2]; // this is the full uri, with query string and fragment
            $requestInfo['version'] = $matches[3];

            // parse uri
            if (preg_match('~^(/.*)(?:\?(.+)?(?:#(.+))?)?\s+$~', $requestInfo['uri'], $matches) === 1) {
                $requestInfo['path'] = $matches[1];
                $requestInfo['fragment'] = $matches[3];

                $queryString = $matches[2];
                if ($queryString !== null) {
                    $requestInfo['queryString'] = $queryString;

                    $queryStringSegments = explode('&', $queryString);
                    $parsedQueryString = [];
                    foreach ($queryStringSegments as $keyValue) {
                        [$key, $value] = explode('=', $keyValue);
                        $parsedQueryString[$key] = $value;
                    }
                    $requestInfo['parsedQueryString'] = $parsedQueryString;
                }
            }

            $isFirstLine = false;
            continue;
        }

        if (preg_match('~^([A-ZA-z0-9-]+): ([^\s]+)\s+$~', $line, $matches) !== 1) {
            // just ignore this header line
            continue;
        }

        $headers[$matches[1]] = $matches[2];
    }

    // TODO parse comma-separated lines into array
    // TODO parse cookies

    $requestInfo['headers'] = $headers;

    $host = $headers['Host'] ?? null;
    $requestInfo['host'] = $host;

    if ($host !== null && preg_match('/:(\d+)$/', $host, $matches) === 1) {
        $port = $matches[1];
        $requestInfo['host'] = str_replace(":$port", '', $host);
        $requestInfo['port'] = (int)$port;
    }

    return $requestInfo;
}