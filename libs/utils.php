<?php
function encodeURI($uri) {
    return preg_replace_callback("{[^0-9a-z_.!~*'();,/?:@&=+$#-]}i", function ($m) {
        return sprintf('%%%02X', ord($m[0]));
    }, $uri);
}

function check_param($key, $array) {
    return (key_exists($key, $array) && $array[$key] != "");
}

function force_param($key, $array) {
    if(!check_param($key, $array)) {
        http_response_code(400);
        die("Bad Request");      
    }
}

function get_full_url($src) {
    $scheme = !key_exists("HTTPS", $_SERVER) || $_SERVER["HTTPS"] == "off" ? "http" : "https";
    if(substr($src, 0, 1) == "/") {
        return $scheme . "://127.0.0.1:" . $_SERVER['SERVER_PORT'] . encodeURI($src) . "?download=1";
    } else {
        return $src;
    }
}
?>
