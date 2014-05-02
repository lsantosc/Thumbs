<?PHP
class GdHandler{

    public $image;
    public $width;
    public $height;
    public $mime;
    public $modified;
    public $etag;

    public function load($input){
        $this->mime = image_type_to_mime_type(exif_imagetype($input));
        $this->image = @imagecreatefromstring(file_get_contents($input));
        imagesavealpha($this->image,true);
        imagealphablending($this->image,true);
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
        $this->modified = filemtime($input);
        $this->etag = md5_file($input);
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

    public function resize($size,$method='width'){
        $diff = $method=='width'?$this->width/$size:$this->height/$size;
        $dw = floor($this->width/$diff);
        $dh = floor($this->height/$diff);
        $new = $this->create($dw,$dh);
        imagecopyresampled($new,$this->image,0,0,0,0,$dw,$dh,$this->width,$this->height);
        $this->image = $new;
        $this->width = $dw;
        $this->height = $dh;
    }

    public function fill($width,$height,$fillColor){
        $diff = max($this->width/$width,$this->height/$height);
        $dw = floor($this->width/$diff);
        $dh = floor($this->height/$diff);
         $temp = $this->create($dw,$dh);
        imagecopyresampled($temp,$this->image,0,0,0,0,$dw,$dh,$this->width,$this->height);
        $this->width = $dw;
        $this->height = $dh;
        $new = $this->create($width,$height);
        $color = $this->hexToRGB($fillColor);
        $alpha = 127-intval(($color['alpha']/100)*127);
        $color = imagecolorallocatealpha($new,$color['red'],$color['green'],$color['blue'],$alpha);
        $dx = ($width/2)-($this->width/2);
        $dy = ($height/2) - ($this->height/2);
        imagefill($new,0,0,$color);
        imagecopyresampled($new,$temp,$dx,$dy,0,0,$this->width,$this->height,$this->width,$this->height);
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