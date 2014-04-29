<?PHP
class ThumbsHelper extends AppHelper{

    public $helpers = array('Html');
    public $path;

    public function get($path){
        $this->path = $path;
        return $this;
    }

    public function crop($size){
        return $this->Html->image("/thumbs/generate/crop/{$size}/{$this->path}");
    }

    public function resize($size){
        return $this->Html->image("/thumbs/generate/resize/{$size}/{$this->path}");
    }

    public function fill($size,$fill){
        return $this->Html->image("/thumbs/generate/fill/{$fill}/{$size}/{$this->path}");
    }

}