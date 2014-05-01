<?PHP
class ThumbsHelper extends AppHelper{

    public $helpers = array('Html');
    public $path;

    public function get($path){
        $this->path = $path;
        return $this;
    }

    public function crop($size,$options=array()){
        return $this->Html->image("/thumbs/crop/{$size}/{$this->path}",$options);
    }

    public function resize($size,$method='width',$options=array()){
        return $this->Html->image("/thumbs/resize/{$size}/{$method}/{$this->path}",$options);
    }

    public function fill($size,$fill,$options=array()){
        return $this->Html->image("/thumbs/fill/{$size}/{$fill}/{$this->path}",$options);
    }

}