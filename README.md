Symfony ffmpeg bundle
=====================

This bundle provides a simple wrapper for the [PHP_FFmpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg) library, 
exposing it as a Symfony service.

Based on [pulse00/ffmpeg-bundle](https://github.com/pulse00/ffmpeg-bundle) by [pulse00](https://github.com/pulse00)

### Usage Example

Configure which ffmpeg binary to use in `config.yml`:


``` yaml
  php_ffmpeg:
    ffmpeg_binary: /usr/bin/ffmpeg
    ffprobe_binary: /usr/bin/ffprobe
    binary_timeout: 300 # Use 0 for infinite
    threads_count: 4
```


Using the service:

```php
	$ffmpeg = $this->get('php_ffmpeg');

	// Open video
	$video = $ffmpeg->open('/your/source/folder/input.avi');
	
	// Resize to 1280x720
	$video
        ->filters()
        ->resize(new Dimension(1280, 720), ResizeFilter::RESIZEMODE_INSET)
        ->synchronize();
        
        // Grab A screenshot
        $video
        ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
        ->save('/PATH/frame.jpg');

	// Start transcoding and save video
	$video->save(new FMpeg\Format\Video\X264(), '/PATH/video.mp4');
```
