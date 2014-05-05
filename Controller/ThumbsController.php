<?php
class ThumbsController extends ThumbsAppController{

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
        if(Configure::read('debug') == 0) $engine->save($config['thumb']);
        $engine->show();
    }

    public function resize(){
        $engine = $this->getEngine();
        $config = $this->request->params['image'];
        $engine->load($config['path']);
        $engine->resize($config['size'],$config['method']);
        if(Configure::read('debug') == 0) $engine->save($config['thumb']);
        $engine->show();
    }

    public function fill(){
        $engine = $this->getEngine();
        $config = $this->request->params['image'];
        $engine->load($config['path']);
        $engine->fill($config['width'],$config['height'],$config['color']);
        if(Configure::read('debug') == 0) $engine->save($config['thumb']);
        $engine->show();
    }

}