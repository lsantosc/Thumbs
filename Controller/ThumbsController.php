<?php
class ThumbsController extends ThumbsAppController{

    protected $config;
    public $autoRender = false;

    private function getEngine(){
        if(extension_loaded('Imagick') && class_exists('Imagick')){
            App::import('Vendor','Thumbs.Imagick');
            return new ImagickHandler();
        } else {
            App::import('Vendor','Thumbs.Gd');
            return new GdHandler();
        }
    }

    public function crop(){
        $engine = $this->getEngine();
        $config = $this->request->params['image'];
        $engine->load($config['path']);
        $engine->crop($config['width'],$config['height']);
        $engine->save($config['thumb']);
        $engine->show();
    }

    public function resize(){
        $engine = $this->getEngine();
        $config = $this->request->params['image'];
        $engine->load($config['path']);
        $engine->resize($config['size'],$config['method']);
        $engine->save($config['thumb']);
        $engine->show();
    }

    public function fill(){
        $engine = $this->getEngine();
        $config = $this->request->params['image'];
        $engine->load($config['path']);
        $engine->fill($config['width'],$config['height'],$config['fill']);
        $engine->save($config['thumb']);
        $engine->show();
    }

}