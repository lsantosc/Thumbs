<?PHP
class GdImage{

    public $image;
    public $width;
    public $height;
    public $mime;

    public function load($input){
        $this->mime = image_type_to_mime_type(exif_imagetype($input));
        $this->image = @imagecreatefromstring(file_get_contents($input));

        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    public function crop($width,$height){
        $dw = $width;
        $dh = $height;
        $this->resize($width,$height,'min');
        $sx = ($this->width/2) - ($width/2);
        $sy = ($this->height/2) - ($height/2);
        $new = imagecreatetruecolor($width,$height);
        $alpha = imagecolorallocatealpha($new,0,0,0,127);
        imagesavealpha($new,true);
        imagealphablending($new,true);
        imagefill($new,0,0,$alpha);
        imagecopyresampled($new,$this->image,0,0,$sx,$sy,$width,$height,$width,$height);
        $this->image=$new;
        $this->width=$width;
        $this->height=$height;
    }

    public function resize($width,$height,$type='max'){
        $diff = $type=='min'?min($this->width/$width,$this->height/$height):max($this->width/$width,$this->height/$height);
        $dw = floor($this->width/$diff);
        $dh = floor($this->height/$diff);
        $new = imagecreatetruecolor($dw,$dh);
        $alpha = imagecolorallocatealpha($new,0,0,0,127);
        imagesavealpha($new,true);
        imagealphablending($new,true);
        imagefill($new,0,0,$alpha);
        imagecopyresampled($new,$this->image,0,0,0,0,$dw,$dh,$this->width,$this->height);
        $this->image = $new;
        $this->width = $dw;
        $this->height = $dh;
    }

    public function fill($width,$height,$fillColor){
        $this->resize($width,$height);
        $new = imagecreatetruecolor($width,$height);
        $alpha = imagecolorallocatealpha($new,0,0,0,127);
        imagesavealpha($new,true);
        imagealphablending($new,true);
        imagefill($new,0,0,$alpha);
        $r = hexdec(substr($fillColor,0,2));
        $g = hexdec(substr($fillColor,2,2));
        $b = hexdec(substr($fillColor,4,2));
        $color = imagecolorallocate($new,$r,$g,$b);
        imagefill($new,0,0,$color);
        $dx = ($width/2)-($this->width/2);
        $dy = ($height/2) - ($this->height/2);
        imagecopymerge($new,$this->image,$dx,$dy,0,0,$this->width,$this->height,100);
        $this->image = $new;
        $this->width = $width;
        $this->height = $height;
    }

    public function show(){
        header("Content-Type: {$this->mime}");
        switch($this->mime){
            case "image/jpeg": imagejpeg($this->image); break;
            case "image/png": imagepng($this->image); break;
            case "image/gif": imagegif($this->image); break;
            default: imagepng($this->image); break;
        }
        imagedestroy($this->image);
        exit;
    }
    public function save($destination,$quality = 90){
        $dir = dirname($destination);
        if(!file_exists($dir)) mkdir($dir,0777,true);
        switch($this->mime){
            case "image/jpeg": imagejpeg($this->image,$destination,$quality); break;
            case "image/png": imagepng($this->image,$destination); break;
            case "image/gif": imagegif($this->image,$destination); break;
            default: imagepng($this->image,$destination,$quality); break;
        }
    }
}