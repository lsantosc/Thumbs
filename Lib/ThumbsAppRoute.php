<?PHP
class ThumbsAppRoute extends CakeRoute{

    protected $config;

    public function parse($url) {
        $url = parent::parse($url);
        if(empty($url)) return false;
        $path = implode(DS,$url['pass']);
        $url['image']['path'] = realpath(APP.WEBROOT_DIR.DS.$path);
        $image = &$url['image'];
        $image['size'] = $url['size'];
        if(!$image['path']) return false;
        $image['md5'] = md5_file($image['path']);
        $image['extension'] = pathinfo($image['path'],PATHINFO_EXTENSION);
        $image['modified'] = filemtime($image['path']);
        $image['thumb'] = APP.'tmp'.DS.'thumbs'.DS.$url['action'].DS.$image['size'].DS."{$image['md5']}.{$image['extension']}";
        if(!empty($url['side'])) $image['thumb'] = APP.'tmp'.DS.'thumbs'.DS.$url['action'].DS.$image['size'].DS.$url['side'].DS."{$image['md5']}.{$image['extension']}";
        if(!empty($url['fill'])) $image['thumb'] = APP.'tmp'.DS.'thumbs'.DS.$url['action'].DS.$image['size'].DS.$url['fill'].DS."{$image['md5']}.{$image['extension']}";

        $image['mime'] = image_type_to_mime_type(exif_imagetype($image['path']));

        $image['thumb_dir'] = dirname($image['thumb']);

        $this->config = realpath(APP."Config/thumbs.php")?
            include realpath(APP."Config/thumbs.php"):
            include realpath(APP."Plugin/Thumbs/Config/thumbs.php");

        return $url;
    }


}