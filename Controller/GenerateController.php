<?php
class GenerateController extends ThumbsAppController{

    private $Image;
    public $autoRender = false;

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->config = $this->readConfig($request);
        App::import('Vendor','Thumbs.GdImage');
        $this->Image = new GdImage();
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

}