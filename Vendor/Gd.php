<?PHP
class GdHandler{

    public $image;
    public $width;
    public $height;
    public $mime;
    public $crop_position = array();
    public $extension;
    public $path;

    public function load($input){
        $this->mime = image_type_to_mime_type(exif_imagetype($input));
        $this->image = @imagecreatefromstring(file_get_contents($input));
        imagesavealpha($this->image,true);
        imagealphablending($this->image,true);
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
        $this->extension = @strtolower(pathinfo($input, PATHINFO_EXTENSION));
        $this->path = $input;
    }

    public function rotate($degrees)
    {
        $image = $this->image;
        $transColor = imagecolorallocatealpha($image, 255, 255, 255, 127);
        $image = imagerotate($image, $degrees, $transColor, 1);
        imagesavealpha($image, true);
        $this->image = $image;
    }

    public function crop($width,$height) {
        $this->calculateCropPositions($width,$height);
        $new = $this->create($width,$height);
        imagecopyresampled($new, $this->image, -$this->crop_position[0], -$this->crop_position[1], 0, 0, $this->crop_position[2], $this->crop_position[3], $this->width, $this->height);
        $this->image = $new;
        $this->width = $width;
        $this->height = $height;
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


    /*public function fill($width, $height, $fillColor)
    {
        $temp = $this->createFromType();
        imagecopyresampled($temp,$this->image,0,0,0,0,$dw,$dh,$this->width,$this->height);
        $color = $this->hexToRGB($fillColor);
        $alpha = 127-intval(($color['alpha']/100)*127);

        $new = $this->create($width, $height);
        $color = imagecolorallocatealpha($new, $color['red'], $color['green'], $color['blue'], $alpha);
        imagefill($new,0,0,$color);

        if ( ($this->width / $width ) > ( $this->height / $height ) )
        {
            $fator = $this->width / $width;
        } else {
            $fator = $this->height / $height;
        }

        $dw = $this->width / $fator;
        $dh = $this->height / $fator;

        // copia com o novo tamanho, centralizando
        $dx = ( $width - $dw ) / 2;
        $dy = ( $height - $dh ) / 2;
        imagecopyresampled($new, $temp, $dx, $dy, 0, 0, $dw, $dh, $width, $height);

        $this->image = $new;
        $this->width = $width;
        $this->height = $height;
    }*/

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

    public function createFromType() {
        switch($this->extension)
        {
            case 'gif':
                return imagecreatefromgif($this->path);
                break;
            case 'jpg':
                return imagecreatefromjpeg($this->path);
                break;
            case 'png':
                return imagecreatefrompng($this->path);
                break;
            case 'bmp':
                return $this->imagecreatefrombmp($this->path);
                break;
            default:
                trigger_error('Invalid File', E_USER_ERROR);
                break;
        }
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
        if(!is_writable($dir)) return false;
        switch($this->mime){
            case "image/jpeg": imagejpeg($this->image,$destination,$quality); break;
            case "image/png": imagepng($this->image,$destination); break;
            case "image/gif": imagegif($this->image,$destination); break;
            default: imagepng($this->image,$destination,$quality); break;
        }
    }

    private function calculateCropPositions($width,$height)
    {
        // 50% of real sizes
        $hm = $this->height / $height;
        $wm = $this->width / $width;

        // 50% of new sizes
        $h_height = $height / 2;
        $h_width  = $width / 2;

        // calculate

        if ($wm > $hm) {
            $this->crop_position[2] = $this->width / $hm;
            $this->crop_position[3] = $height;
            $this->crop_position[0] = ($this->crop_position[2] / 2) - $h_width;
            $this->crop_position[1] = 0;
        }
        // if width is less or equal of height
        elseif (($wm <= $hm)) {
            $this->crop_position[2] = $width;
            $this->crop_position[3] = $this->height / $wm;
            $this->crop_position[0] = 0;
            $this->crop_position[1] = ($this->crop_position[3] / 2) - $h_height;
        }

    }

    public function imagecreatefrombmp($filename) {
        //Ouverture du fichier en mode binaire
        if (! $f1 = fopen($filename,"rb")) return FALSE;

        //1 : Chargement des ent?tes FICHIER
        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
        if ($FILE['file_type'] != 19778) return FALSE;

        //2 : Chargement des ent?tes BMP
        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
            '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
            '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
        $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
        if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] = 4-(4*$BMP['decal']);
        if ($BMP['decal'] == 4) $BMP['decal'] = 0;

        //3 : Chargement des couleurs de la palette
        $PALETTE = array();
        if ($BMP['colors'] < 16777216)
        {
            $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
        }

        //4 : Cr?ation de l'image
        $IMG = fread($f1,$BMP['size_bitmap']);
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
        $P = 0;
        $Y = $BMP['height']-1;
        while ($Y >= 0)
        {
            $X=0;
            while ($X < $BMP['width'])
            {
                if ($BMP['bits_per_pixel'] == 24)
                    $COLOR = @unpack("V",substr($IMG,$P,3).$VIDE);
                elseif ($BMP['bits_per_pixel'] == 16)
                {
                    $COLOR = @unpack("n",substr($IMG,$P,2));
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 8)
                {
                    $COLOR = @unpack("n",$VIDE.substr($IMG,$P,1));
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 4)
                {
                    $COLOR = @unpack("n",$VIDE.substr($IMG,floor($P),1));
                    if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 1)
                {
                    $COLOR = @unpack("n",$VIDE.substr($IMG,floor($P),1));
                    if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]          >>7;
                    elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                    elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                    elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                    elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                    elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                    elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                    elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                else
                    return FALSE;
                imagesetpixel($res,$X,$Y,$COLOR[1]);
                $X++;
                $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P+=$BMP['decal'];
        }

        //Fermeture du fichier
        fclose($f1);

        return $res;

    } // fim function image from BMP



}
