Thumbs
======

Thumbs plugin for CakePHP 2.x

Cria o arquivo de config na pasta app/Config/thumbs.php com o seguinte comando:

``` PHP
return array(
    'crop'=>array(
        'tiny'=>array('width'=>60,'height'=>60),
        'small'=>array('width'=>150,'height'=>150),
        'medium'=>array('width'=>300,'height'=>300),
        'big'=>array('width'=>500,'height'=>500),
    ),
    'resize'=>array(
        'tiny'=>array('width'=>100,'height'=>300),
        'small'=>array('width'=>200,'height'=>300),
    )
);
```