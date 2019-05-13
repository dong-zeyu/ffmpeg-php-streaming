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
    "audio" => "Content-Type: text/vtt",
    "subtitle" => "Content-Type: video/MP2T"
);
$ffmpeg = "ffmpeg";
$ffprob = "ffprobe";
?>
