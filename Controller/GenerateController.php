<?php
class GenerateController extends ThumbsAppController{

    private $Image;
    protected $config;
    public $autoRender = false;

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->config = $this->readConfig($request);
        $this->output($this->config['thumb']);
        if(extension_loaded('Imagick') && class_exists('Imagick')) App::import('Vendor','Thumbs.Imagick');
        else App::import('Vendor','Thumbs.Gd');
        $this->Image = new Thumbclass();
    }

    private function output($path){
        if(!file_exists($path)) return false;
        $mime = image_type_to_mime_type(exif_imagetype($path));
        header("Content-Type: {$mime}");
        exit(file_get_contents($path));
    }

    public function crop(){
        $this->Image->load($this->config['image']);
        $this->Image->crop($this->config['size']['width'],$this->config['size']['height']);
        $this->Image->save($this->config['thumb']);
        $this->Image->show();
    }

    public function resize(){
        $this->Image->load($this->config['image']);
        $this->Image->resize($this->config['size']['width'],$this->config['size']['height']);
        $this->Image->save($this->config['thumb']);
        $this->Image->show();
    }

    public function fill(){
        $this->Image->load($this->config['image']);
        $this->Image->fill($this->config['size']['width'],$this->config['size']['height'],$this->config['fill']);
        $this->Image->save($this->config['thumb']);
        $this->Image->show();
    }

    protected function readConfig($request){

        if($request['action'] == 'fill') $fill = array_shift($request->params['pass']);
        $conf = realpath(APP."/Config/thumbs.php");
        $conf = $conf?include($conf):include(realpath('../Config/thumbs.php'));
        $sizeName = array_shift($request->params['pass']);
        $sizes = @$conf[$request['action']][$sizeName];
        if(!$sizes) throw new NotFoundException(__('Tamanho não permitido'));
        $size['width'] = $sizes[0];
        $size['height'] = $sizes[1];
        $imagePath = APP.WEBROOT_DIR.DS.implode(DS,$request->params['pass']);
        if(!file_exists($imagePath)) throw new NotFoundException(__('Imagem não encontrada'));
        $md5 = md5(file_get_contents($imagePath));
        $return = array(
            'url'=>implode('/',$request->params['pass']),
            'image'=> APP.WEBROOT_DIR.DS.implode(DS,$request->params['pass']),
            'thumb'=>TMP.'thumbs'.DS.$request['action'].DS.$sizeName.DS.$md5.'.'.pathinfo($imagePath,PATHINFO_EXTENSION),
            'md5'=>$md5,
            'size'=>$size,
        );
        if(!empty($fill)){
            $color = @$conf['colors'][$fill];
            if(!$color) throw new NotFoundException('Cor de fundo da imagem não permitido');
            $return['fill'] = $color;
            $return['thumb'] = TMP.'thumbs'.DS.$request['action'].DS.$fill.DS.$sizeName.DS.$md5.'.'.pathinfo($imagePath,PATHINFO_EXTENSION);
        }
        return $return;
    }

}