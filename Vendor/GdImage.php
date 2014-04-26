<?PHP
class GdImage{

    public $image;
    public $width;
    public $height;

    public function load($input){
        $this->image = imagecreatefromstring(file_get_contents($input));
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
        header('Content-Type: Image/Jpeg');
    }

    public function crop($width,$height){
        $this->resize($width,$height);
        $diff = min($this->width-$width,$this->height-$height);
        pre($diff);
    }

    public function resize($width,$height,$type='min'){
        $original = $this->image;
        $dx = $this->width-$width;
        $dy = $this->height-$height;
    }

    public function fill($width,$height,$fillColor){
    }

    public function show(){}
    public function save($destination){}

}