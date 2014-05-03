<?php
//Import the ThumbsAppRouter and extends it
App::import('Lib','Thumbs.ThumbsAppRoute');
class FillRoute extends ThumbsAppRoute{

    public function parse($url){

        //Gets url params, if not match return false
        $url = parent::parse($url);
        if(empty($url)) return false;

        //If action is cached, pass through
        if($url['action']=='cached') return $url;

        //Get sizes from config file
        $size = @$this->config[$url['action']]['sizes'][$url['size']];

        //Get colors from config file
        $color = @$this->config[$url['action']]['colors'][$url['fill']];

        //If config file doesnt  have it´s size or it´s color, then return false
        if(empty($size) || empty($color)) return false;

        //Get´s width, height and fill color from config.
        $url['image']['width'] = array_shift($size);
        $url['image']['height'] = array_shift($size);
        $url['image']['fill'] = $color;
        return $url;
    }

}