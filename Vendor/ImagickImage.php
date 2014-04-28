<?PHP
class ImagickImage {

    private $imagick;

    public $width;
    public $height;
    public $mime;


    public function load($input){
        $this->imagick = new Imagick($input);
        $this->width = $this->imagick->getimagewidth();
        $this->height = $this->imagick->getimageheight();
        $this->mime = $this->imagick->getimagemimetype();
    }

    public function crop($width,$height){
        $this->imagick->cropthumbnailimage($width,$height);
        $this->width=$width;
        $this->height=$height;
    }

    public function resize($width,$height){
        $width = ($width>$height)?$width:0;
        $height = ($height>$width)?$height:0;
        $this->imagick->thumbnailimage($width,$height);
        $this->width = $this->imagick->getimagewidth();
        $this->height = $this->imagick->getimageheight();

    }

    public function fill($width,$height,$fillColor){
        $fillColor = "#$fillColor";
        $this->width = $width;
        $this->height = $height;
        $this->imagick->scaleimage($width,$height,true);
        $this->imagick->setimagebackgroundcolor($fillColor);
        $w = $this->imagick->getImageWidth();
        $h = $this->imagick->getImageHeight();
        $this->imagick->extentimage($this->width,$this->height,($w-$this->width)/2,($h-$this->height)/2);
    }

    public function show(){
        header("Content-type: {$this->mime}");
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