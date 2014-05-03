<?PHP
class ThumbsAppRoute extends CakeRoute{

    protected $config;

    public function parse($url) {

        //If thumbs cache directive sends to webroot, then the image will be cached at webroot´s directory
        if(Configure::read('Thumbs.cache') == 'webroot'){
            $cachedir = WWW_ROOT.str_replace(array('/','\\'),DS,$url);
            $cachedir = str_replace('\\\\',DS,$cachedir);
        }

        //Parses the URL, if not pass, just return false.
        $url = parent::parse($url);
        if(empty($url)) return false;

        //Get the image path on disk
        $path = implode(DS,$url['pass']);
        $url['image']['path'] = realpath(WWW_ROOT.$path);

        //Reference for $this->request->params['image'] on the controller
        $image = &$url['image'];

        //Set´s the size
        $image['size'] = $url['size'];

        //If the image doesnt exists on disk, then the route returns false and go to next router directive
        if(!$image['path']) return false;

        //Gets the MD5 of the file, for caching into TMP directory
        $image['md5'] = md5_file($image['path']);

        //Gets the file extension
        $image['extension'] = pathinfo($image['path'],PATHINFO_EXTENSION);

        //Build the thumb path on disk
        $image['thumb'] = APP.'tmp'.DS.'thumbs'.DS.$url['action'].DS.$image['size'].DS."{$image['md5']}.{$image['extension']}";
        if(!empty($url['side'])) $image['thumb'] = APP.'tmp'.DS.'thumbs'.DS.$url['action'].DS.$image['size'].DS.$url['side'].DS."{$image['md5']}.{$image['extension']}";
        if(!empty($url['fill'])) $image['thumb'] = APP.'tmp'.DS.'thumbs'.DS.$url['action'].DS.$image['size'].DS.$url['fill'].DS."{$image['md5']}.{$image['extension']}";
        //If the cache directive sends to webroot, them $cachedir will be used
        if(!empty($cachedir)) $image['thumb'] = $cachedir;

        //gets image mime type
        $image['mime'] = image_type_to_mime_type(exif_imagetype($image['path']));

        //gets the modified date of the image
        $image['modified'] = filemtime($image['path']);

        //If the thumb exists, then send directly to cached action on ThumbsController (only if cache is sent to tmp directory)
        if(file_exists($image['thumb'])){
            $url['action'] = 'cached';
            return $url;
        }

        //Gets the thumb directory
        $image['thumb_dir'] = dirname($image['thumb']);

        //Gets the configuration directives for sizes and colors, if not created on main Config directory, will get from Thumbs plugin directory
        $this->config = realpath(APP."Config/thumbs.php")?
            include realpath(APP."Config/thumbs.php"):
            include realpath(APP."Plugin/Thumbs/Config/thumbs.php");

        //If a cache doesnt exists, send $url directive to next router class...
        return $url;
    }


}