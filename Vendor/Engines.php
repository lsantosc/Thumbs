<?PHP
class Engines {

    public $image;
    public $width;
    public $height;
    public $mime;
    public $modified;
    public $etag;

    public function cache(){
        if(!empty($this->modified) && !empty($this->etag)){
            $ifEtag = (isset($_SERVER['HTTP_IF_NONE_MATCH']))?trim($_SERVER['HTTP_IF_NONE_MATCH']):false;
            $ifModified = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))?$_SERVER['HTTP_IF_MODIFIED_SINCE']:false;
            header("Cache-Control: public");
            header("Etag: {$this->etag}");
            header("Last-Modified: ".gmdate("D, d M Y H:i:s", $this->modified)." GMT");
            if((!empty($ifModified) && @strtotime($ifModified) === $this->modified) || (!empty($ifEtag) && $ifEtag == $this->etag)){
                header("HTTP/1.1 304 Not Modified");
                exit;
            }
        }
    }

} 