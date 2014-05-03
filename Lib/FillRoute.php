<?php
App::import('Lib','Thumbs.ThumbsAppRoute');
class FillRoute extends ThumbsAppRoute{

    public function parse($url){
        $url = parent::parse($url);
        if(empty($url)) return false;
        if($url['action']=='cached') return $url;
        $size = @$this->config[$url['action']]['sizes'][$url['size']];
        $color = @$this->config[$url['action']]['colors'][$url['fill']];
        if(empty($size) || empty($color)) return false;
        $url['image']['width'] = array_shift($size);
        $url['image']['height'] = array_shift($size);
        $url['image']['fill'] = $color;
        return $url;
    }

}