<?PHP
class ImagickHandler {

    private $imagick;

    public $width;
    public $height;
    public $mime;
    public $modified;
    public $etag;

    public function load($input){
        $this->imagick = new Imagick($input);
        $this->width = $this->imagick->getimagewidth();
        $this->height = $this->imagick->getimageheight();
        $this->mime = $this->imagick->getimagemimetype();
        $this->modified = filemtime($input);
        $this->etag = md5_file($input);
    }

    public function crop($width,$height){
        $this->imagick->cropthumbnailimage($width,$height);
        $this->width=$width;
        $this->height=$height;
    }

    public function resize($size,$method='width'){
        $diff = $method=='width'?$this->width/$size:$this->height/$size;
        $width = floor($this->width/$diff);
        $height = floor($this->height/$diff);
        $this->imagick->thumbnailimage($width,$height);
        $this->width = $this->imagick->getimagewidth();
        $this->height = $this->imagick->getimageheight();

    }

    public function fill($width,$height,$fillColor){
        $this->width = $width;
        $this->height = $height;
        $this->imagick->scaleimage($width,$height,true);
        $this->imagick->setimagebackgroundcolor($this->hexToRGB($fillColor));
        $w = $this->imagick->getImageWidth();
        $h = $this->imagick->getImageHeight();
        $this->imagick->extentimage($this->width,$this->height,($w-$this->width)/2,($h-$this->height)/2);
    }

    public function hexToRGB($color){
        $r = hexdec(substr($color,0,2));
        $g = hexdec(substr($color,2,2));
        $b = hexdec(substr($color,4,2));
        $a = is_numeric(substr($color,7,3))?substr($color,7,3)/100:100/100;
        return "rgba($r,$g,$b,$a)";
    }

    public function show($path = false){
        if($path) $this->load($path);
        $ifetag = (isset($_SERVER['HTTP_IF_NONE_MATCH']))?trim($_SERVER['HTTP_IF_NONE_MATCH']):false;
        $ifmodified = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))?$_SERVER['HTTP_IF_MODIFIED_SINCE']:false;
        header("Content-type: {$this->mime}");
        header("Cache-Control: public");
        header("Etag: {$this->etag}");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s", $this->modified)." GMT");
        if((!empty($ifmodified) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $this->modified) || (!empty($ifetag) && $ifetag == $this->etag)){
            header("HTTP/1.1 304 Not Modified");
            exit;
        }
        echo $this->imagick->getimage();
        $this->imagick->destroy();
        exit;
    }

    public function save($destination,$quality = 90){
        $dir = dirname($destination);
        if(!file_exists($dir)) mkdir($dir,0777,true);
        $this->imagick->setcompression(Imagick::COMPRESSION_JPEG);
        $this->imagick->setcompressionquality($quality);
        $this->imagick->writeimage($destination);
    }


}