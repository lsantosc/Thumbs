<?PHP
class ThumbsHelper extends AppHelper{

    public $helpers = array('Html');
    public $path;

    public function get($path){
        $this->path = $path;
        return $this;
    }

    public function crop($size,$options=array()){
        return $this->Html->image("/thumbs/generate/crop/{$size}/{$this->path}",$options);
    }

    public function resize($size,$options=array()){
        return $this->Html->image("/thumbs/generate/resize/{$size}/{$this->path}",$options);
    }

    public function fill($size,$fill,$options=array()){
        return $this->Html->image("/thumbs/generate/fill/{$fill}/{$size}/{$this->path}",$options);
    }

}