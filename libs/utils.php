<?php
require_once("config.php");

function run($cmd, &$stdout, $passthru) {
    ignore_user_abort(true);
    $proc = proc_open($cmd, [1 => ["pipe", "w"]], $pipes);
    if (is_resource($proc)) {
        if($passthru) {
            fpassthru($pipes[1]);
        } else {
            $stdout = stream_get_contents($pipes[1]);
        }
        $ret = proc_close($proc);
        if($ret != 0) {
            http_response_code(500);
            echo "$cmd\n";
            echo "Return Code: $ret\n";
            exit;
        }
    } else {
        http_response_code(500);
        die("Error While running:\n" . $cmd);
    }
}

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
        return $scheme . "://127.0.0.1:" . $_SERVER['SERVER_PORT'] . encodeURI($src);
    } else {
        return $src;
    }
}

function get_auth_str() {
    return key_exists("HTTP_AUTHORIZATION", $_SERVER) ? "-headers \"Authorization: " . $_SERVER['HTTP_AUTHORIZATION'] . "\"" : "";
}
?>
