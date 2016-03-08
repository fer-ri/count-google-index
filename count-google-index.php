<?php

/*
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

/*
 * Taken from http://www.binarytides.com/validate-domain-name-filter_var-function-php/
 */
function filter_var_domain($domain)
{
    if (stripos($domain, 'http://') === 0) {
        $domain = substr($domain, 7);
    }

    ///Not even a single . this will eliminate things like abcd, since http://abcd is reported valid
    if (! substr_count($domain, '.')) {
        return false;
    }

    if (stripos($domain, 'www.') === 0) {
        $domain = substr($domain, 4);
    }

    $again = 'http://'.$domain;

    return filter_var($again, FILTER_VALIDATE_URL);
}

$url = isset($_GET['url']) ? $_GET['url'] : null;

if (! $url) {
    return responseJson([
        'error' => 'URL is required',
    ], 403);
}

if (! filter_var_domain($url)) {
    return responseJson([
        'error' => 'URL is not valid',
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
