Symfony ffmpeg bundle
=====================

This bundle provides a simple wrapper for the [PHP_FFmpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg) library, 
exposing it as a Symfony service.

Based on [pulse00/ffmpeg-bundle](https://github.com/pulse00/ffmpeg-bundle) by [pulse00](https://github.com/pulse00)

### Usage Example

Enable the extension in the AppKernel

``` php
            ....
            new sujayjaju\FFmpegBundle\PhpFFmpegBundle(),
            ....
```

Configure which ffmpeg binary to use in `config.yml`:


``` yaml
php_ffmpeg:
    ffmpeg_binary: /usr/bin/ffmpeg
    ffprobe_binary: /usr/bin/ffprobe
    binary_timeout: 300 # Use 0 for infinite
    threads_count: 4
```


Using the service:

``` php
	$ffmpeg = $this->get('php_ffmpeg.ffmpeg');

	// Open video
	$video = $ffmpeg->open('/your/source/folder/input.avi');
	
	// Resize to 640x480
	$video
        ->filters()
        ->resize(new Dimension(640, 480), ResizeFilter::RESIZEMODE_INSET)
        ->synchronize();
        
    // Create a thumbnail
    $video
        ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
        ->save('/PATH/frame.jpg');

	// Start transcoding and save video
	$video->save(new FMpeg\Format\Video\X264(), '/PATH/video.mp4');
```



### Sample Integration With [1up-lab/OneupUploaderBundle](https://github.com/1up-lab/OneupUploaderBundle)

* Step 1: Create Your Upload Listener Class

``` php
namespace Application\YourBundle\EventListener;

use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Exception\RuntimeException;
use Oneup\UploaderBundle\Event\PostUploadEvent;

class YourUploadListener
{
    protected $doctrine;
    protected $ffmpeg;


    public function __construct($doctrine, $ffmpeg)
    {
        $this->doctrine = $doctrine;
        $this->ffmpeg = $ffmpeg;
    }

    public function onUpload(PostUploadEvent $event)
    {
        $file_path = $event->getFile()->getPathname();
        $response = $event->getResponse();
        $video = null;
        try{
            $ffmpeg = $this->ffmpeg->open($file_path);
        }catch(InvalidArgumentException $e){
            $response->setSuccess(false);
            $response->setError("Could not load file");
            $response['preventRetry'] = true;
            unlink($file_path);
            return;
        }catch(RuntimeException $e){
            $response->setSuccess(false);
            $response->setError("Invalid File");
            $response['preventRetry'] = true;
            unlink($file_path);
            return;
        }

        // Sample code to check if video was uploaded
        $streams = $ffmpeg->getStreams();
        foreach($streams as $stream){
            if($stream->get('codec_type') == 'video'){
                $video = $stream;
            }
        }

        if(is_null($video)){
            $response->setSuccess(false);
            $response->setError("Invalid Video");
            $response['preventRetry'] = true;
            unlink($file_path);
            return;
        }

        // Do what you want with the video
        // $video
    }
}
```

* Step 2: Define your service

``` yaml

    application.your_upload_listener:
        class: Application\YourBundle\EventListener\YourUploadListener
        arguments: [@doctrine, @php_ffmpeg.ffmpeg]
        tags:
          -  { name: kernel.event_listener, event: oneup_uploader.post_upload, method: onUpload }

```
