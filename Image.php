<?php

class Image {

    private $img;
    private $manipImg;
    private $resizeWidth;
    private $resizeHeight;
    private $watermark;
    private $newImage;
    private $type;
    private $width;
    private $height;
    private $size;
    private $path;

    function __construct($path) {
        $this->path = $path;
        $this->type = $this->determineType();
        $this->img = $this->loadImage($this->type);
        list($width, $height) = getimagesize($this->path);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->size = filesize($this->path);
    }

    public function setHeight($height) {
        $this->height = $height;
    }

    public function getHeight() {
        return $this->height;
    }

    public function setWidth($width) {
        $this->width = $width;
    }
    
    public function getImgResource() {
        return $this->img;
    }

    public function getWidth() {
        return $this->width;
    }

    private function determineType() {
        $typeCode = exif_imagetype($this->path);

        switch ($typeCode) {
            case 1:
                return "image/gif";
                // $this->img = imagecreatefromgif($this->path);
                break;
            case 2:
                // JPEG
                return "image/jpeg";
                //  $this->img = imagecreatefromjpeg($this->path);
//                $this->didOpenSuccessfully($this->img);
                break;
            case 3:
                // PNG 
                return "image/png";
                //  $this->img = imagecreatefrompng($this->path);
                break;
            case 4:
                throw new Exception("SWF images aren't supported at this time.");
                break;
            case 5:
                throw new Exception("PSD images aren't supported at this time.");
                break;
            case 6:
                // BMP @todo add support for this filetype
                break;
            case 7:
                // TIFF_II @todo add support for this filetype
                break;
            case 8:
                // TIFF_MM @todo add support for this filetype
                break;
            case 9:
                // JPC @todo add support for this filetype
                break;
            case 10:
                // JP2 @todo add support for this filetype
                break;
            case 11:
                // JPX @todo add support for this filetype
                break;
            case 12:
                // JB2 @todo add support for this filetype
                break;
            case 13:
                // SWC @todo add support for this filetype
                break;
            case 14:
                // IFF @todo add support for this filetype
                break;
            case 15:
                // WBMP @todo add support for this filetype
                break;
            case 16:
                // XBM @todo add support for this filetype
                break;
            case 17:
                // ICO @todo add support for this filetype
                break;
            default:
                throw new Exception("File is not an image");
        }
    }

    private function loadImage($type, $path = null) {
        switch ($this->type) {
            case 'image/jpeg':
                if (isset($path)) {
                    return imagecreatefromjpeg($path);
                } else {
                     return imagecreatefromjpeg($this->path);
                }
                break;
            case "image/png":
                if (isset($path)) {
                    return imagecreatefrompng($path);
                } else {
                    return imagecreatefrompng($this->path);
                }
                break;
            case "image/gif":
                if (isset($path)) {
                    return imagecreatefromgif($path);
                } else {
                    return imagecreatefromgif($this->path);
                }
                break;
        }
    }

    public function createNewImage($width, $height) {
        $this->newImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($this->newImage, $this->img, 0, 0, 0, 0, $this->resizeWidth, $this->resizeHeight, $this->width, $this->height);
        $this->img = $this->newImage;
    }

    public function resizeExact($width, $height) {
        $this->createNewImage($width, $height);
    }

    public function resizeMaxHeight($height) {
        $this->resizeWidth = $this->resizeWidthByHeight($height);
        $this->resizeHeight = $height;
        $this->createNewImage($this->resizeWidth, $this->resizeHeight);
    }

    public function resizeMaxWidth($width) {
        $this->resizeHeight = $this->resizeHeightByWidth($width);
        $this->resizeWidth = $width;
        $this->createNewImage($this->resizeWidth, $this->resizeHeight);
    }

    private function didOpenSuccessfully($resource) {
        if (!$resource) {
            throw Exception("Couldn't load the specified image.");
        }
    }

    // Credit: https://paulund.co.uk/resize-image-class-php
    private function resizeHeightByWidth($width) {
        return floor(($this->height / $this->width) * $width);
    }

    private function resizeWidthByHeight($height) {
        return floor(($this->width / $this->height) * $height);
    }

    private function setPNGCompression($quality) {
        return ($quality / 100) * 9;
    }

    public function saveImage($path, $quality, $download = false) {
        switch ($this->type) {
            case 'image/jpeg':
                if (imagetypes() & IMG_JPG) {
                    imagejpeg($this->img, $path, $quality);
                } else {
                    throw Exception("JPEG image handling not enabled in your PHP settings.");
                }
                break;
            case "image/png":
                $compression = $this->setPNGCompression($quality);
                if (imagetypes() & IMG_PNG) {
                    imagepng($this->img, $path, $compression);
                } else {
                    throw Exception("PNG image handling not enabled in your PHP settings.");
                }
                break;
            case "image/gif":
                if (imagetypes() & IMG_GIF) {
                    imagegif($this->img, $path);
                } else {
                    throw Exception("GIF image handling not enabled in your PHP settings.");
                }
                break;
        }
        if ($download) {
            header('Content-Description: File Transfer');
            header("Content-type: application/octet-stream");
            header("Content-disposition: attachment; filename= " . $path . "");
            readfile($path);
        }
        imagedestroy($this->img);
    }

    public function watermarkFromPhoto($watermarkPath, $marginRight, $marginBottom) {
        $this->watermark = new Image($watermarkPath);
        $imgResource = $this->watermark->getImgResource();
        $sx = imagesx($imgResource);
        $sy = imagesy($imgResource);
        $dsx = imagesx($this->img);
        $dsy = imagesy($this->img);
        $offsetRight = ($dsx - $sx - $marginRight);
        $offsetBottom =  ($dsy - $sy - $marginBottom);
        
        imagecopy($this->img, $imgResource, $offsetRight, $offsetBottom, 0, 0, $sx, $sy);
        
    }

    public function watermarkFromText() {
        
    }

}

// Ignore, for testing purposes. 
$myImg = new Image('home.jpg');
$myImg->watermarkFromPhoto("Mushroom2.png", 10, 10);
$myImg->saveImage("/new2.jpg", 100, true);
