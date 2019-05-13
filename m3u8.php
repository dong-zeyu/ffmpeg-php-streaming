<?php
require "libs/ffmpeg.php";

force_param("src", $_GET);
header("Content-Type: application/x-mpegURL");
?>
<?php if(key_exists("type", $_GET)): ?>
<?php
force_param("length", $_GET);
force_param("index", $_GET);
force_param("encoders", $_GET);
$length = $_GET["length"];
$type = $_GET["type"];
if(!in_array($type, $types)) {
    http_response_code(400);
    die("The specific type is not found!");
}
?>
#EXTM3U
#EXT-X-VERSION:3
#EXT-X-ALLOW-CACHE:YES
#EXT-X-MEDIA-SEQUENCE:0
#EXT-X-PLAYLIST-TYPE:VOD
#EXT-X-TARGETDURATION:<?php printf("%s\n", $segment_time[$type]) ?>
<?php
$segment_num = floor($length / $segment_time[$type]);
for($i = 0; $i < $segment_num - 1; $i++) {
    printf("#EXTINF:%.6f,\n", $segment_time[$type]);
    printf("decode.php?src=%s&seg=%d&encoders=%s&index=%s&quality=%s&type=%s\n", rawurlencode($_GET["src"]), $i, $_GET["encoders"], $_GET["index"], $_GET["quality"], $_GET["type"]);
}
printf("#EXTINF:%.6f,\n", $length - $segment_time[$type] * ($segment_num - 1));
printf("decode.php?src=%s&seg=%d&encoders=%s&index=%s&quality=%s&type=%s\n", rawurlencode($_GET["src"]), $segment_num, $_GET["encoders"], $_GET["index"], $_GET["quality"], $_GET["type"]);
?>
#EXT-X-ENDLIST
<?php else: ?>
<?php
$src = $_GET["src"];
$url = get_full_url($src);
if(key_exists("quality", $_GET) && $_GET["quality"] != "") {
    $quality = $_GET["quality"];
} else {
    $quality = "";
}

// Run ffprob to get track info
$data = ffprob_prog($url);

// Get length of video
$length = $data["format"]["duration"];

// Get stream info
$videos = array();
$audios = array();
$subtitles = array();
foreach($data["streams"] as $stream) {
    if($stream["codec_type"] == "video") {
        $video = $stream["index"];
        if($stream["codec_name"] == "h264") {
            $stream["encoder"] = $quality == "" ? "" : "h264";  // If it is already h264 then just copy stream
        } else {
            $stream["encoder"] = "h264";
        }
        array_push($videos, $stream);
    } elseif($stream["codec_type"] == "audio") {
        if($stream["codec_name"] == "aac") {
            $stream["encoder"] = "";  // If it is already aac then just copy stream
        } else {
            $stream["encoder"] = "aac";
        }
        array_push($audios, $stream);
    }
    if($stream["codec_type"] == "subtitle") {
        array_push($subtitles, $stream);
    }
}

// ini_set('xdebug.var_display_max_depth', '20');
// echo var_dump($data);
// die;
$default = true;
?>
#EXTM3U
<?php foreach($subtitles as $stream): ?>
<?php
$name = key_exists("tags", $stream) && key_exists("title", $stream["tags"]) ? $stream["tags"]["title"] : $stream["index"];
$language = key_exists("tags", $stream) && key_exists("language", $stream["tags"]) ? $stream["tags"]["language"] : "";
$default_str = $default ? "YES" : "NO";
$default = false;
?>
#EXT-X-MEDIA:TYPE=SUBTITLES,GROUP-ID="subs",DEFAULT=<?php echo $default_str ?>,NAME="<?php echo $name?>",LANGUAGE="<?php echo $language ?>",URI="<?php printf("m3u8.php?src=%s&index=%d&length=%s&encoders=,,&quality=&type=subtitle", rawurlencode($src), $stream["index"], $length) ?>" 
<?php endforeach ?>
<?php if(count($videos) == 1 && count($audios) == 1): ?>
<?php
$index = array($videos[0]["index"], $audios[0]["index"]);
$encoders = array($videos[0]["encoder"], $audios[0]["encoder"], "");
?>
#EXT-X-STREAM-INF:SUBTITLES="subs"
<?php printf("m3u8.php?src=%s&index=%s&length=%s&encoders=%s&quality=%s&type=video\n", rawurlencode($src), implode(",", $index), $length, implode(",", $encoders), $quality) ?>
<?php else: ?>
<?php foreach($audios as $stream): ?>
<?php
$language = key_exists("tags", $stream) && key_exists("language", $stream["tags"]) ? $stream["tags"]["language"] : "";
$name = key_exists("tags", $stream) && key_exists("title", $stream["tags"]) ? $stream["tags"]["title"] : $language . "(" . $stream["index"] . ")";
?>
#EXT-X-MEDIA:TYPE=AUDIO,GROUP-ID="audios",NAME="<?php echo $name ?>",LANGUAGE="<?php echo $language ?>",URI="<?php printf("m3u8.php?src=%s&index=%d&encoders=%s&length=%s&quality=&type=audio", rawurlencode($src), $stream["index"], implode(",", array("", $stream["encoder"], "")), $length) ?>" 
<?php endforeach ?>
#EXT-X-STREAM-INF:SUBTITLES="subs",AUDIO="audios"
<?php printf("m3u8.php?src=%s&index=%s&length=%s&encoders=%s&quality=%s&type=video\n", rawurlencode($src), $videos[0]["index"] , $length, implode(",", array($videos[0]["encoder"], "", "")), $quality) ?>
<?php endif ?>
<?php endif ?>
