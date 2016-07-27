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
                // Gif
                break;
            case 2:
                // JPEG
                $this->type = "image/jpeg";
                $this->img = imagecreatefromjpeg($this->path);
                $this->didOpenSuccessfully($this->img);
                break;
            case 3: 
                // PNG @todo add support for this filetype
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
    
    
    private function createNewImage($width, $height) {
        $this->newImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($this->newImage, $this->img, 0, 0, 0, 0, $this->resizeWidth, $this->resizeHeight, $this->width, $this->height);
    }
    
    
    private function resizeExact($width, $height) {
        createNewImage($width, $height);
    }
    
    private function resizeMaxHeight($height) {
        $width = $this->resizeWidthByHeight($height);
        createNewImage($width, $height);
    }
    
    private function resizeMaxWidth($width) {
        $height = $this->resizeHeightByWidth($width);
        createNewImage($width, $height);
    }

    private function didOpenSuccessfully($resource) {
        if(!$resource) {
            echo "We had trouble opening your image file.";
        }
        else {
            echo "Image Opened Succcessfully";
        }
    }
    
    
    // Credit: https://paulund.co.uk/resize-image-class-php
    private function resizeHeightByWidth($width) {
        return floor(($this->height / $this->width) * $width);
    }
    
    private function resizeWidthByHeight($height) {
       return floor(($this->width / $this->height) * $height); 
    }
    
    public function saveImage($path, $quality, $download = false) {
        switch($this->type) {
            case 'image/jpg':
                if(imagetypes & IMG_JPG) {
                    imagejpeg($this->newImage, $path, $quality);
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

$myImg = new Image('home.jpg');