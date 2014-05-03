<?PHP
class ThumbsAppRoute extends CakeRoute{

    protected $config;

    public function parse($url) {
        if(Configure::read('Thumbs.cache') == 'webroot') $cachedir = APP.WEBROOT_DIR.str_replace(array('/','\\'),DS,$url);
        $url = parent::parse($url);
        if(empty($url)) return false;
        $path = implode(DS,$url['pass']);
        $url['image']['path'] = realpath(APP.WEBROOT_DIR.DS.$path);
        $image = &$url['image'];
        $image['size'] = $url['size'];
        if(!$image['path']) return false;
        $image['md5'] = md5_file($image['path']);
        $image['extension'] = pathinfo($image['path'],PATHINFO_EXTENSION);

        $image['thumb'] = APP.'tmp'.DS.'thumbs'.DS.$url['action'].DS.$image['size'].DS."{$image['md5']}.{$image['extension']}";
        if(!empty($url['side'])) $image['thumb'] = APP.'tmp'.DS.'thumbs'.DS.$url['action'].DS.$image['size'].DS.$url['side'].DS."{$image['md5']}.{$image['extension']}";
        if(!empty($url['fill'])) $image['thumb'] = APP.'tmp'.DS.'thumbs'.DS.$url['action'].DS.$image['size'].DS.$url['fill'].DS."{$image['md5']}.{$image['extension']}";

        if(!empty($cachedir)) $image['thumb'] = $cachedir;

        $image['mime'] = image_type_to_mime_type(exif_imagetype($image['path']));
        $image['modified'] = filemtime($image['path']);
        if(file_exists($image['thumb'])){
            $url['action'] = 'cached';
            return $url;
        }
        $image['thumb_dir'] = dirname($image['thumb']);
        $this->config = realpath(APP."Config/thumbs.php")?
            include realpath(APP."Config/thumbs.php"):
            include realpath(APP."Plugin/Thumbs/Config/thumbs.php");

        return $url;
    }


}