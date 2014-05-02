<?php
class ThumbsController extends ThumbsAppController{

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
        $ifetag=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
        header("Content-Type: {$config['mime']}");
        header("Cache-Control: public");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s", $config['modified'])." GMT");
        if(@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $config['modified'] || $ifetag == $config['md5']) {
            header("HTTP/1.1 304 Not Modified");
            exit;
        }
        echo file_get_contents($config['thumb']);
        exit;
    }

}