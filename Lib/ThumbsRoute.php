<?PHP
class ThumbsRoute extends CakeRoute{

    public function parse($url) {
        $params = parent::parse($url);
        if(empty($params)) return false;
        $path = realpath(implode(DS,$params['pass']));
        if(empty($path)) return false;

        $thumb = str_replace(array('/','\\','\\\\'),DS,WWW_ROOT.$url);

        $config = realpath(APP.'Config/thumbs.php')?
            include(realpath(APP.'Config/thumbs.php')):
            include(realpath(APP.'Plugin/Thumbs/Config/thumbs.php'));

        switch($params['action']){
            case "crop": $image = $this->_crop($config,$path,$params,$thumb); break;
            case "prop": $image = $this->_prop($config,$path,$params,$thumb); break;
            case "resize": $image = $this->_resize($config,$path,$params,$thumb); break;
            case "fill": $image = $this->_fill($config,$path,$params,$thumb); break;
            case "rotate": $image = $this->_rotate($config, $path, $params, $thumb); break;
        }

        if(empty($image)) return false;
        $params['image'] = $image;
        return $params;

    }

    private function _rotate($config, $path, $params, $thumb)
    {
        $degrees = @$config['rotate'][$params['degrees']];

        if(empty($degrees)) return false;
        return array(
            'mime'=>image_type_to_mime_type(exif_imagetype($path)),
            'path'=>$path,
            'thumb'=>$thumb,
            'degrees' => $degrees
        );
    }

    private function _prop($config, $path, $params, $thumb)
    {
        $size = @$config['prop'][$params['size']];
        if(empty($size)) return false;
        return array(
            'mime'=>image_type_to_mime_type(exif_imagetype($path)),
            'path'=>$path,
            'thumb'=>$thumb,
            'width'=>array_shift($size),
            'height'=>array_shift($size),
        );
    }

    private function _crop($config,$path,$params,$thumb){
        $size = @$config['crop'][$params['size']];
        if(empty($size)) return false;
        return array(
            'mime'=>image_type_to_mime_type(exif_imagetype($path)),
            'path'=>$path,
            'thumb'=>$thumb,
            'width'=>array_shift($size),
            'height'=>array_shift($size),
        );
    }

    private function _resize($config,$path,$params,$thumb){
        $size = @$config['resize'][$params['size']];
        if(empty($size)) return false;
        return array(
            'mime'=>image_type_to_mime_type(exif_imagetype($path)),
            'path'=>$path,
            'thumb'=>$thumb,
            'size'=>$size,
            'method'=>$params['side'],
        );
    }

    private function _fill($config,$path,$params,$thumb){
        $size = @$config['fill']['sizes'][$params['size']];
        $color = @$config['fill']['colors'][$params['color']];
        if(empty($size)||empty($color)) return false;
        return array(
            'mime'=>image_type_to_mime_type(exif_imagetype($path)),
            'path'=>$path,
            'thumb'=>$thumb,
            'width'=>array_shift($size),
            'height'=>array_shift($size),
            'color'=>$color,
        );
    }

}