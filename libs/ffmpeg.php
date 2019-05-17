<?php
include "utils.php";
include "config.php";

function ffmpeg_prog($args) {
    global $content_type, $format, $segment_time, $types, $ffmpeg;
    $type = $_GET["type"];
    if(!in_array($type, $types)) {
        http_response_code(400);
        die("The specific type is not found!");    
    }

    $input = "-i " . "\"" . $args["input"] . "\"";
    $format = "-f " . $format[$type];
    $t = "-t ". $segment_time[$type];
    $start_time = $args["seg"] * $segment_time[$type];
    $ss = "-ss " . $start_time;
    $output_offset = "-output_ts_offset " . $start_time;
    $auth = $auth = key_exists("HTTP_AUTHORIZATION", $_SERVER) ? "-headers \"Authorization: " . $_SERVER['HTTP_AUTHORIZATION'] . "\"" : "";
    $cv = check_param("cv", $args) ? "-c:v " . $args["cv"] : "-c:v copy";
    $cv = $cv == "-c:v h264" ? $cv . " -profile:v high" : $cv;
    $ca = check_param("ca", $args) ? "-c:a " . $args["ca"] : "-c:a copy";
    $cs = check_param("cs", $args) ? "-c:s " . $args["cs"] : "-c:s copy";
    $quality = check_param("quality", $args) ? "-s " . $args["quality"] : "";
    $maps = array();
    if(key_exists("maps", $args)){
        foreach($args["maps"] as $index) {
            array_push($maps, "-map 0:" . $index);
        }
    }
    $maps = implode(" ", $maps);

    $ffmpeg_args = array(
        $ffmpeg,
        $ss,
        $auth,
        $input,
        $t,
        $maps,
        $cv,
        $ca,
        $cs,
        "-preset ultrafast",
        "-level 5.0",
        "-crf 28",
        $quality,
        $output_offset,
        $format,
        "-",
        // "2>&1"
    );
    // echo implode(" ", $ffmpeg_args) . "\n";
    // die;
    set_time_limit($segment_time[$type]);
    header($content_type[$_GET["type"]]);
    passthru(implode(" ", $ffmpeg_args), $ret);
    if($ret != 0) {
        http_response_code(500);
        echo implode(" ", $ffmpeg_args) . "\n";
        echo "Return Code: " . $ret;
    }
}

function ffprob_prog($input) {
    global $ffprob;
    $auth = $auth = key_exists("HTTP_AUTHORIZATION", $_SERVER) ? "-headers \"Authorization: " . $_SERVER['HTTP_AUTHORIZATION'] . "\"" : "";
    $cmd = array(
        $ffprob,
        "-show_format",
        "-show_streams",
        "-print_format json",
        $auth,
        "\"" . $input . "\""
    );
    $data = array();
    exec(implode(" ", $cmd), $data, $ret);
    $data = implode("\n", $data);
    if($ret != 0) {
        http_response_code(500);
        echo implode(" ", $cmd) . "\n";
        echo $data . "\n";
        echo $ret . "\n";
        exit;
    }
    $data = json_decode($data, true);

    return $data;
}
?>
