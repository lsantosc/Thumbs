<?php
//Import the ThumbsAppRouter and extends it
App::import('Lib','Thumbs.ThumbsAppRoute');
class CropRoute extends ThumbsAppRoute{

    public function parse($url) {

        //Parses the URL, and if nothing came, return false
        $url = parent::parse($url);
        if(empty($url)) return false;

        //If action sends to cached action, just pass through
        if($url['action'] == 'cached') return $url;

        //Get´s the sizes of config file (from /app/Config/thumbs.php or /app/Plugin/Thumbs/Config/thumbs.php)
        $size = @$this->config[$url['action']][$url['size']];

        //If doesnt exists size on config file, then the size is not allowed and returns false
        if(empty($size)) return false;

        //Send width and height to $this->request->params['image']
        $url['image']['width'] = array_shift($size);
        $url['image']['height'] = array_shift($size);

        //Return params to the controller
        return $url;
    }

}