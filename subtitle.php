<?php
require_once("libs/utils.php");
require_once("libs/config.php");

force_param("src", $_GET);

$ffmpeg_args = array(
    $ffmpeg,
    "-v fatal",
    get_auth_str(),
    "-i '" . get_full_url($_GET['src']) . "'",
    "-f webvtt",
    "-",
);
$cmd = implode(" ", $ffmpeg_args);
run($cmd, $stdout, true);

header($content_type["subtitle"]);
echo $stdout;
?>
