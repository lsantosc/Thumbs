<?php
class GenerateController extends ThumbsAppController{

    protected $sizes = array(
        'crop'=>array(
            'tiny'=>array(50,50),
            'small'=>array(100,100),
            'medium'=>array(200,200),
        ),
        'resize'=>array(
            'tiny'=>array(100,100),
            'small'=>array(300,300),
            'medium'=>array(600,600),
        ),
        'fill'=>array(
            'tiny'=>array(50,50),
            'small'=>array(100,100),
            'medium'=>array(200,200),
        ),
        'colors'=>array(

        ),
    );
    private $Image;
    protected $config;
    public $autoRender = false;

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->config = $this->readConfig($request);
        App::import('Vendor','Thumbs.GdImage');
        if(extension_loaded('Imagick')) App::import('Vendor','Thumbs.ImagickImage');
        $this->Image = (class_exists('ImagickImage'))? new ImagickImage() : new GdImage();
        if(file_exists($this->config['thumb'])) $this->show($this->config['thumb']);
    }

    public function show($destination){
        $this->Image->load($destination);
        $this->Image->show();
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

        if($request['action'] == 'fill'){
            $fill = array_shift($request->params['pass']);
        }

        //GET THE SIZES AND SIZENAME
        $conf = realpath(APP."/Config/thumbs.php");
        $conf = $conf?include($conf):$this->sizes;
        $sizeName = array_shift($request->params['pass']);
        $sizes = @$conf[$request['action']][$sizeName];
        if(!$sizes) throw new NotFoundException(__('Tamanho não permitido'));
        $size['width'] = $sizes[0];
        $size['height'] = $sizes[1];

        //Get the image and thumb paths
        $imagePath = APP.WEBROOT_DIR.DS.implode(DS,$request->params['pass']);
        if(!file_exists($imagePath)) throw new NotFoundException(__('Imagem não encontrada'));

        //Get the MD5 of the image
        $md5 = md5(file_get_contents($imagePath));


        //Return the data
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