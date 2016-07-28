<?php

class Image {
    private $img;
    private $manipImg;
    private $resizeWidth;
    private $resizeHeight;
    private $newImage;
    private $type;
    private $width;
    private $height;
    private $size;
    private $path;
    
    function __construct($path) {   
        
       $this->path = $path;    
       $this->determineType();
       
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
    
    public function getWidth() {
        return $this->width;
    }
    
    private function determineType() {
        $typeCode = exif_imagetype($this->path);
        
        switch($typeCode) {
            case 1:
                $this->type = "image/gif";
                $this->img = imagecreatefromgif($this->path);
                break;
            case 2:
                // JPEG
                $this->type = "image/jpeg";
                $this->img = imagecreatefromjpeg($this->path);
//                $this->didOpenSuccessfully($this->img);
                break;
            case 3: 
                // PNG 
                $this->type = "image/png";
                $this->img = imagecreatefrompng($this->path);
                break;
            case 4:
                // SWF @todo add support for this filetype
                break;
            case 5:
                // PSD @todo add support for this filetype / restrict resizing
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
    
    
    public function createNewImage($width, $height) {
        $this->newImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($this->newImage, $this->img, 0, 0, 0, 0, $this->resizeWidth, $this->resizeHeight, $this->width, $this->height);
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
        if(!$resource) {
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
        switch($this->type) {
            case 'image/jpeg':
                if(imagetypes() & IMG_JPG) {
                    imagejpeg($this->newImage, $path, $quality);
                }
                else {
                    throw Exception("JPEG image handling not enabled in your PHP settings.");
                }
                break;
            case "image/png":
                $compression = $this->setPNGCompression($quality);
                if(imagetypes() & IMG_PNG) {
                    imagepng($this->newImage, $path, $compression);
                }
                else {
                    throw Exception("PNG image handling not enabled in your PHP settings.");
                }
                break;
            case "image/gif":
                if(imagetypes() & IMG_GIF) {
                    imagegif($this->newImage, $path); 
                }
                else {
                    throw Exception("GIF image handling not enabled in your PHP settings.");
                }
                break;
        }
            if($download)
	    {
	    	header('Content-Description: File Transfer');
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename= ".$path."");
		readfile($path);
	    }
    }
    
}



// Ignore, for testing purposes. 
$myImg = new Image('Rotating_Sphere.gif');
$myImg->resizeMaxWidth(200);
$myImg->saveImage("/new2.gif", 100, true);