<?PHP
class Thumbs{

    private static function getConfig(){
       return realpath(APP.'Config/thumbs.php')?
            include(realpath(APP.'Config/thumbs.php')):
            include(realpath(APP.'Plugin/Thumbs/Config/thumbs.php'));
    }

    public static function clear($image = null){
        $generated = self::generatedThumbsPaths($image);
        foreach($generated as $path){
            if(!unlink($path)) {
                throw new Exception('Não foi possível excluir a imagem '.$path);
            }
        }
        return $generated;
    }

    public static function generatedThumbsPaths($path){
        $crops = self::generatedCropsPath($path);
        $resizes = self::generatedResizesPath($path);
        $fills = self::generatedFillsPath($path);
        $rotate = self::generatedRotatePaths($path);
        return array_merge($crops,$resizes,$fills, $rotate);
    }

    public static function generatedRotatePaths($image) {
        $config = self::getConfig();
        $config = @$config['rotate'];
        $paths = array();
        foreach($config as $k => $value){
            $path = realpath(WWW_ROOT."thumbs/rotate/$k/$image");
            if(!empty($path)) $paths[] = $path;
        }
        return $paths;
    }

    private static function generatedCropsPath($image){
        $config = self::getConfig();
        $config = @$config['crop'];
        $paths = array();
        foreach($config as $k=>$value){
            $path = realpath(WWW_ROOT."thumbs/crop/$k/$image");
            if(!empty($path)) $paths[] = $path;
        }
        return $paths;
    }

    private static function generatedResizesPath($image){
        $config = self::getConfig();
        $config = @$config['crop'];
        $paths = array();
        foreach($config as $k=>$value){
            $width = realpath(WWW_ROOT."thumbs/resize/$k/width/$image");
            $height = realpath(WWW_ROOT."thumbs/resize/$k/height/$image");
            if(!empty($width)) $paths[] = $width;
            if(!empty($height)) $paths[] = $height;
        }
        return $paths;
    }

    private static function generatedFillsPath($image){
        $config = self::getConfig();
        $sizes = @$config['fill']['sizes'];
        $colors = @$config['fill']['colors'];
        $paths = array();
        foreach($sizes as $size=>$values){
            foreach($colors as $color=>$values){
                $path = realpath(WWW_ROOT."thumbs/fill/$size/$color/$image");
                if(!empty($path)) $paths[] = $path;
            }
        }
        return $paths;
    }
}