<?php

/**
 * Really really simple function return response as json
 * Ferri Sutanto
 * 25 Apr 2015 12:01PM.
 */
if (! function_exists('responseJson')) {
    function responseJson($data = [], $statusCode = '200')
    {
        http_response_code($statusCode);

        header('Content-Type: application/json');

        echo json_encode($data);

        die;
    }
}

$url = isset($_GET['url']) ? $_GET['url'] : null;

if (! $url) {
    return responseJson([
        'error' => 'URL is required',
    ], 403);
}

$googleUrl = 'https://www.google.com/search?hl=en&gbv=1&q=site:'.urlencode($url);

$get = @file_get_contents($googleUrl);

$pattern = '#<div class="sd" id="resultStats">About (.*?) results</div>#';

$count = 0;

if (preg_match($pattern, $get, $match)) {
    $count = (int) str_replace(',', '', $match[1]);
}

return responseJson([
        'data' => [
            'count' => $count,
            'url' => $url,
            'google' => $googleUrl,
        ],
    ]);
