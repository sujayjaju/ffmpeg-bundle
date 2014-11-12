<?php

namespace sujayjaju\FFmpegBundle;

use sujayjaju\FFmpegBundle\DependencyInjection\PhpFFmpegExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PhpFFmpegBundle extends Bundle
{
    //override function to allow "php_ffmpeg" alias
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new PhpFFmpegExtension();
        }

        return $this->extension;
    }
}
