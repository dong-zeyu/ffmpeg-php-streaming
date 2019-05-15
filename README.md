# FFMpeg-PHP Video Streaming Tool

This is an instant-decoding and streaming tool written in PHP based on FFMpeg. It uses HLS protocol to stream videos.

## Requirement

- PHP
- FFMpeg with h264 support

## Usage

The following URL will output a m3u8 file for playing.

    m3u8.php?src=<path-to-video-file>&quality=<quality>

1. `<path-to-video-file>` must be an absolute URL relative to your domain (start with '/') when accessing resources in the same domain.
2. For `<quality>` option, please refer to [FFMpeg Video Size](https://ffmpeg.org/ffmpeg-all.html#Video-size).

### Config

See libs/config.php. You may need to change path for `$ffmpeg` and `$ffmprob`.

## Example

Here is an example using video-js with hls plugin:

```html
<head>
  <link href="https://vjs.zencdn.net/7.5.4/video-js.css" rel="stylesheet">
</head>
<body>
  <video id="my-video" class="video-js vjs-default-skin" controls>
    <source src="m3u8.php?src=/Sample_video.rmvb&quality=hd480" type="application/x-mpegURL">
  </video>
  <script src='https://vjs.zencdn.net/7.5.4/video.js'></script>
  <script src="https://unpkg.com/@videojs/http-streaming@1.10.2/dist/videojs-http-streaming.min.js"></script>
  <script>
    player = videojs("my-video");
  </script>
</body>
```

where `Sample_video.rmvb` located in `http(s)://<your-domain>/Sample_video.rmvb`.
