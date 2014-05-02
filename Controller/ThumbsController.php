<?php
class ThumbsController extends ThumbsAppController{

    private $Image;
    protected $config;
    public $autoRender = false;


    private function output($path){
        if(!file_exists($path)) return false;
        $mime = image_type_to_mime_type(exif_imagetype($path));
        header("Content-Type: {$mime}");
        exit(file_get_contents($path));
    }

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

    public function cached(){
        $config = $this->request->params['image'];
        //$filemtime = filemtime($config['path']);
        //header("Etag: $filemtime");
        //header("HTTP/1.1 304 Not Modified");
        header("Content-Type: {$config['mime']}");
        echo file_get_contents($config['thumb']);
        exit;
    }

}