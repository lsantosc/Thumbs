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
            'medium'=>array('width'=>500,'height'=>500),
            'big'=>array('width'=>1000,'height'=>1000),
        )
    );

    protected $headers = array(
        'jpg'=>"Image/jpeg",
        'jpeg'=>"Image/jpeg",
        'png'=>"Image/png",
        'gif'=>"Image/gif",
    );

    protected $config;



    protected function show($path){
        $extension = pathinfo($path,PATHINFO_EXTENSION);
        $type = $this->headers[$extension];
        header("Content-Type: $type");
        echo file_get_contents($path);
        exit;
    }

    protected function readConfig($request){

        if($request['action'] == 'fill'){
            $fill = array_shift($request->params['pass']);
        }
        //GET THE SIZES AND SIZENAME
        $sizes = realpath(APP."/Config/thumbs.php");
        $sizes = $sizes?include($sizes):$this->sizes;
        $sizeName = array_shift($request->params['pass']);
        $size = @$sizes[$request['action']][$sizeName];

        //IF SIZE NOT ON LIST, ERROR!!
        if(!$size) throw new NotFoundException(__('Tamanho não permitido'));

        //Get the image and thumb paths
        $imagePath = APP.WEBROOT_DIR.DS.implode(DS,$request->params['pass']);
        if(!file_exists($imagePath)) throw new NotFoundException(__('Imagem não encontrada'));

        //Get the MD5 of the image
        $md5 = md5(file_get_contents($imagePath));


        //Return the data
        $return = array(
            'url'=>implode('/',$request->params['pass']),
            'image'=> APP.WEBROOT_DIR.DS.implode(DS,$request->params['pass']),
            'thumb'=>TMP.$request['controller'].DS.$sizeName.DS.$md5.'.'.pathinfo($imagePath,PATHINFO_EXTENSION),
            'md5'=>$md5,
            'size'=>$size,
        );
        if(!empty($fill)) $return['fill'] = $fill;
        return $return;
    }
}