<?PHP
class ThumbsAppController extends AppController{

    protected $sizes = array(
        'crop'=>array(
            'tiny'=>array('width'=>50,'height'=>50),
            'small'=>array('width'=>100,'height'=>100),
            'medium'=>array('width'=>300,'height'=>300),
            'big'=>array('width'=>500,'height'=>500),
        ),
        'resize'=>array(
            'tiny'=>array('width'=>100,'height'=>300),
            'small'=>array('width'=>200,'height'=>300),
        )
    );

    protected $size;
    protected $path;

    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $config = realpath(APP."/Config/thumbs.php");
        if($config) $this->sizes = include($config);
    }

    public function size(){
        $arguments = func_get_args();
        $this->size = array_shift($arguments);
        $this->size = $this->sizes[$this->request->params['controller']][$this->size];
        $this->path = implode("/",$arguments);
        $this->path = realpath(APP.WEBROOT_DIR.DS.$this->path);
        pr($this->path);
        pr($this->size);
    }

}