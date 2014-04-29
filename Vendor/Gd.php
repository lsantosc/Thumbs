<?PHP
class Thumbclass{

    public $image;
    public $width;
    public $height;
    public $mime;

    public function load($input){
        $this->mime = image_type_to_mime_type(exif_imagetype($input));
        $this->image = @imagecreatefromstring(file_get_contents($input));
        imagesavealpha($this->image,true);
        imagealphablending($this->image,true);
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    public function crop($width,$height){
        $dw = $width;
        $dh = $height;
        $this->resize($width,$height,'min');
        $sx = ($this->width/2) - ($width/2);
        $sy = ($this->height/2) - ($height/2);
        $new = $this->create($width,$height);
        imagecopyresampled($new,$this->image,0,0,$sx,$sy,$width,$height,$width,$height);
        $this->image=$new;
        $this->width=$width;
        $this->height=$height;
    }

    public function resize($width,$height,$type='max'){
        $diff = $type=='min'?min($this->width/$width,$this->height/$height):max($this->width/$width,$this->height/$height);
        $dw = floor($this->width/$diff);
        $dh = floor($this->height/$diff);
        $new = $this->create($dw,$dh);
        imagecopyresampled($new,$this->image,0,0,0,0,$dw,$dh,$this->width,$this->height);
        $this->image = $new;
        $this->width = $dw;
        $this->height = $dh;
    }

    public function fill($width,$height,$fillColor){
        $this->resize($width,$height);
        $new = $this->create($width,$height);
        $color = $this->hexToRGB($fillColor);
        $alpha = 127-intval(($color['alpha']/100)*127);
        $color = imagecolorallocatealpha($new,$color['red'],$color['green'],$color['blue'],$alpha);
        $dx = ($width/2)-($this->width/2);
        $dy = ($height/2) - ($this->height/2);
        imagefill($new,0,0,$color);
        imagecopyresampled($new,$this->image,$dx,$dy,0,0,$this->width,$this->height,$this->width,$this->height);
        $this->image = $new;
        $this->width = $width;
        $this->height = $height;
    }

    public function hexToRGB($color){
        $r = hexdec(substr($color,0,2));
        $g = hexdec(substr($color,2,2));
        $b = hexdec(substr($color,4,2));
        $a = is_numeric(substr($color,7,3))?substr($color,7,3):100;
        return array('red'=>$r,'green'=>$g,'blue'=>$b,'alpha'=>$a);
    }

    public function create($width,$height){
        imagesavealpha($this->image,true);
        imagealphablending($this->image,true);
        $image = imagecreatetruecolor($width,$height);
        $alpha = imagecolorallocatealpha($image,0,0,0,127);
        imagealphablending($image,true);
        imagesavealpha($image,true);
        imagefill($image,0,0,$alpha);
        return $image;
    }

    public function show($path = false){
        if($path) $this->load($path);
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