<?php
require "libs/ffmpeg.php";

force_param("src", $_GET);
force_param("seg", $_GET);
force_param("encoders", $_GET);
force_param("index", $_GET);
force_param("type", $_GET);

$encoders = explode(",", $_GET["encoders"]);
ffmpeg_prog(array(
    "input" => get_full_url($_GET["src"]),
    "seg" => $_GET["seg"],
    "type" => $_GET["type"],
    "cv" => $encoders[0],
    "ca" => $encoders[1],
    "cs" => $encoders[2],
    "maps" => explode(",", $_GET["index"]),
    "quality" => $_GET["quality"]
));
?>
