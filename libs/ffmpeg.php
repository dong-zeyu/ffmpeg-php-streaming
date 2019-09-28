<?php
require_once("utils.php");
require_once("config.php");

function ffmpeg_prog($args) {
    global $content_type, $format, $segment_time, $types, $ffmpeg, $default_args;
    $type = $_GET["type"];
    if(!in_array($type, $types)) {
        http_response_code(400);
        die("The specific type is not found!");    
    }

    $input = "-i '${args['input']}'";
    $format = "-f " . $format[$type];
    $t = "-t ". $segment_time[$type];
    $start_time = $args["seg"] * $segment_time[$type];
    $ss = "-ss " . $start_time;
    $output_offset = "-output_ts_offset " . $start_time;
    $auth = get_auth_str();
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
        $default_args,
        $quality,
        $output_offset,
        $format,
        "-",
    );
    $cmd = implode(" ", $ffmpeg_args);
    set_time_limit($segment_time[$type]);
    header($content_type[$_GET["type"]]);
    run($cmd, $stdout, true);
}

function ffprob_prog($input) {
    global $ffprob;
    $auth = get_auth_str();
    $ffprob_args = array(
        $ffprob,
        "-show_format",
        "-show_streams",
        "-print_format json",
        $auth,
        "'$input'"
    );
    $cmd = implode(" ", $ffprob_args);
    run($cmd, $stdout, false);

    return json_decode($stdout, true);
}
?>
