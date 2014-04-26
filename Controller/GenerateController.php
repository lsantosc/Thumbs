<?php
class GenerateController extends ThumbsAppController{

    private $Image;

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        //Lê o arquivo de config, caso não existir irá usar o padrão
        $this->config = $this->readConfig($request);
        if(file_exists($this->config['thumb'])) $this->show($this->config['thumb']);

        App::import('Vendor','Thumbs.GdImage');
        $this->Image = new GdImage();
    }


    public function crop(){
        $this->Image->load($this->config['image']);
        $this->Image->crop($this->config['size']['width'],$this->config['size']['height']);
    }

    public function resize(){}

    public function fill(){
        pre($this->config);
    }

}