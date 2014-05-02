<?php
App::import('Lib','Thumbs.ThumbsAppRoute');
class ResizeRoute extends ThumbsAppRoute{

    public function parse($url) {
        $url = parent::parse($url);
        if(empty($url)) return false;
        $config = @$this->config[$url['action']][$url['size']];
        if(empty($config)) return false;
        $url['image']['size'] = $config;
        $url['image']['method'] = $url['side'];
        return $url;
    }


}