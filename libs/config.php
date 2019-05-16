<?php
$types = array(
    "video",
    "audio",
    "subtitle"
);
$segment_time = array(
    "video" => 5,
    "audio" => 25,
    "subtitle" => 50
);
$format = array(
    "video" => "mpegts",
    "audio" => "mpegts",
    "subtitle" => "webvtt"
);
$content_type = array(
    "video" => "Content-Type: video/MP2T",
    "audio" => "Content-Type: video/MP2T",
    "subtitle" => "Content-Type: text/vtt"
);
$ffmpeg = "ffmpeg";
$ffprob = "ffprobe";
?>
