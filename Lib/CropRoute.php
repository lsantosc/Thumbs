<?php
App::import('Lib','Thumbs.ThumbsAppRoute');
class CropRoute extends ThumbsAppRoute{

    public function parse($url) {
        $url = parent::parse($url);
        if(empty($url)) return false;

        $size = @$this->config[$url['action']][$url['size']];
        if(empty($size)) return false;
        $url['image']['width'] = array_shift($size);
        $url['image']['height'] = array_shift($size);
        return $url;
    }

}